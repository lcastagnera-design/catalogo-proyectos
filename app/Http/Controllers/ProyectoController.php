<?php

namespace App\Http\Controllers;

use App\Models\MinisterioSecretaria;
use App\Models\Proyecto;
use App\Models\Tecnologia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    /** Valores permitidos para tipo_componente. */
    public const TIPO_COMPONENTE = [
        'API',
        'BACKEND',
        'FRONT',
        'FULLSTACK',
        'Dependencia Propia (Npm, maven, etc)',
        'MOBILE',
        'BASE DE DATOS',
    ];

    /** Valores permitidos para exposicion_internet. */
    public const EXPOSICION_INTERNET = [
        'Externo',
        'Interno',
        'No expuesta',
    ];

    /**
     * Lista los proyectos con paginación simple y filtros rápidos (US-02).
     */
    public function index(Request $request)
    {
        $proyectos = Proyecto::query()
            ->with('areaSolicitante')
            ->withCount('componentes')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('nombre_proyecto', 'like', '%'.$request->string('search').'%');
            })
            // US-04: los proyectos archivados NO aparecen por defecto en el listado.
            ->when(! $request->boolean('archivados'), function ($q) {
                $q->where('estado', '!=', Proyecto::ESTADO_ARCHIVADO);
            })
            // US-02: filtro por área solicitante (nivel3 / repartición).
            ->when($request->filled('area'), function ($q) use ($request) {
                $q->whereHas('areaSolicitante', function ($areaQ) use ($request) {
                    $areaQ->where('nivel3', $request->string('area'));
                });
            })
            // US-02: filtro por estado (activos o archivado).
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado', $request->string('estado'));
            })
            // US-02: filtro por trimestre del created_at (Q1..Q4).
            ->when($request->filled('trimestre'), function ($q) use ($request) {
                $this->applyTrimestre($q, $request->string('trimestre'));
            })
            // US-02: ordenamiento por última actualización.
            ->when($request->string('orden') === 'updated_asc', function ($q) {
                $q->orderBy('updated_at', 'asc');
            }, function ($q) use ($request) {
                $q->when($request->string('orden') === 'updated_desc', function () use ($q) {
                    $q->orderBy('updated_at', 'desc');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Opciones para los filtros del index.
        $areas = MinisterioSecretaria::where('nivel3', '!=', '-')
            ->distinct()
            ->orderBy('nivel3')
            ->pluck('nivel3')
            ->values();

        $estados = Proyecto::ESTADOS_ACTIVOS;

        return view('proyectos.index', compact('proyectos', 'areas', 'estados'));
    }

    /**
     * Muestra el formulario vacío (US-03).
     */
    public function create()
    {
        return view('proyectos.create', $this->formOptions());
    }

    /**
     * Valida y crea el proyecto junto a sus componentes (1 a N) (US-03).
     */
    public function store(Request $request)
    {
        $data = $this->validateProyecto($request);

        $proyecto = Proyecto::create([
            'nombre_proyecto' => $data['nombre_proyecto'],
            'nombre_proyecto_marca' => $data['nombre_proyecto_marca'],
            'area_solicitante_id' => $data['area_solicitante_id'],
            // US-03: estado editable al crear (nunca archivado).
            'estado' => $data['estado'],
        ]);

        foreach ($data['componentes'] as $componente) {
            $proyecto->componentes()->create($this->componenteData($componente));
        }

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Muestra el detalle del proyecto con sus componentes.
     */
    public function show(Proyecto $proyecto)
    {
        $proyecto->load(['areaSolicitante', 'componentes']);

        return view('proyectos.show', compact('proyecto'));
    }

    /**
     * Muestra el formulario con los datos cargados (US-04 editar).
     */
    public function edit(Proyecto $proyecto)
    {
        $proyecto->load('componentes');

        return view('proyectos.edit', array_merge(
            $this->formOptions(),
            ['proyecto' => $proyecto]
        ));
    }

    /**
     * Valida y actualiza el proyecto, re-sincronizando sus componentes (US-03/04).
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        $data = $this->validateProyecto($request);

        $proyecto->update([
            'nombre_proyecto' => $data['nombre_proyecto'],
            'nombre_proyecto_marca' => $data['nombre_proyecto_marca'],
            'area_solicitante_id' => $data['area_solicitante_id'],
            // US-03: estado editable al editar (nunca archivado desde el form).
            'estado' => $data['estado'],
        ]);

        // Estrategia robusta: se reemplazan los componentes por la lista enviada.
        $proyecto->componentes()->delete();

        foreach ($data['componentes'] as $componente) {
            $proyecto->componentes()->create($this->componenteData($componente));
        }

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * US-04: Archiva el proyecto (baja lógica). NO borra el registro de la BD.
     */
    public function destroy(Proyecto $proyecto)
    {
        $proyecto->update(['estado' => Proyecto::ESTADO_ARCHIVADO]);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto archivado correctamente. No se elimina, queda guardado para auditoría.');
    }

    /**
     * US-05: Cambia el estado de un proyecto directamente desde el listado.
     */
    public function cambiarEstado(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'estado' => ['required', 'string', 'in:'.implode(',', Proyecto::ESTADOS_ACTIVOS)],
        ]);

        $proyecto->update(['estado' => $data['estado']]);

        // Infraestructura mínima para notificaciones automáticas al equipo (US-05):
        // En el futuro, acá se dispararía un evento/notificación al cambiar el estado,
        // ej. event(new ProyectoEstadoCambiado($proyecto, $estadoAnterior)).
        // Sin sistema real de equipos por ahora.

        return redirect()->back()
            ->with('success', 'Estado del proyecto actualizado a "'.$proyecto->estadoLabel().'".');
    }

    /**
     * US-06: Dashboard con gráficos (Chart.js) por repartición y por estado.
     */
    public function dashboard()
    {
        // Proyectos no archivados.
        $proyectos = Proyecto::query()
            ->with('areaSolicitante')
            ->noArchivados()
            ->get();

        // Gráfico de torta: cantidad de proyectos por repartición (nivel3 · nivel4).
        $porReparticion = $proyectos
            ->groupBy(function (Proyecto $p) {
                return $this->reparticionLabel($p->areaSolicitante);
            })
            ->map->count()
            ->sortDesc();

        // Gráfico de barras: cantidad de proyectos por estado.
        $porEstado = $proyectos
            ->groupBy('estado')
            ->map->count();

        $totalProyectos = $proyectos->count();

        // Orden de estados para las barras (activos).
        $estadosOrden = Proyecto::ESTADOS_ACTIVOS;

        return view('dashboard', [
            'etiquetasReparticion' => array_values($porReparticion->keys()->toArray()),
            'datosReparticion' => array_values($porReparticion->values()->toArray()),
            'etiquetasEstado' => $estadosOrden,
            'labelsEstados' => array_map(fn ($e) => Proyecto::ESTADOS_LABELS[$e], $estadosOrden),
            'datosEstado' => array_map(fn ($e) => $porEstado[$e] ?? 0, $estadosOrden),
            'totalProyectos' => $totalProyectos,
        ]);
    }

    /**
     * US-07: Reporte en PDF del portfolio (listado con estado + resumen).
     */
    public function reporte()
    {
        $proyectos = Proyecto::query()
            ->with('areaSolicitante')
            ->withCount('componentes')
            ->noArchivados()
            ->orderBy('estado')
            ->orderBy('nombre_proyecto')
            ->get();

        // Resumen por repartición y por estado.
        $porReparticion = $proyectos
            ->groupBy(fn (Proyecto $p) => $this->reparticionLabel($p->areaSolicitante))
            ->map->count()
            ->sortDesc();

        $porEstado = $proyectos
            ->groupBy('estado')
            ->map->count();

        $estadosOrden = Proyecto::ESTADOS_ACTIVOS;

        $pdf = Pdf::loadView('reporte', [
            'proyectos' => $proyectos,
            'porReparticion' => $porReparticion,
            'porEstado' => $porEstado,
            'estadosOrden' => $estadosOrden,
            'totalProyectos' => $proyectos->count(),
        ]);

        return $pdf->download('reporte-proyectos.pdf');
    }

    /**
     * Endpoint AJAX: devuelve las filas de nivel4 (secretarías/dependencias)
     * correspondientes a un nivel3 (ministerio/área). Cada opción lleva el id
     * de la fila de ministerio_secretarias a guardar en area_solicitante_id.
     */
    public function areas(Request $request)
    {
        $data = $request->validate([
            'nivel3' => 'required|string|max:255',
        ]);

        $rows = MinisterioSecretaria::where('nivel3', $data['nivel3'])->get(['id', 'nivel3', 'nivel4']);

        $options = $rows->map(function (MinisterioSecretaria $row) {
            $label = $row->nivel4 !== null && $row->nivel4 !== '-'
                ? $row->nivel4
                : $row->nivel3;

            return [
                'id' => $row->id,
                'label' => $label,
            ];
        });

        return response()->json($options);
    }

    /**
     * Datos compartidos por el partial del formulario (create y edit).
     *
     * @return array<string, mixed>
     */
    protected function formOptions(): array
    {
        // Ministerios / áreas únicos de nivel 3 (excluye marcadores '-').
        $areasNivel3 = MinisterioSecretaria::where('nivel3', '!=', '-')
            ->distinct()
            ->orderBy('nivel3')
            ->pluck('nivel3')
            ->values();

        $tecnologias = Tecnologia::orderBy('categoria')->orderBy('nombre')->get();

        return [
            'areasNivel3' => $areasNivel3,
            'tecnologias' => $tecnologias,
            'tipoComponentes' => self::TIPO_COMPONENTE,
            'exposiciones' => self::EXPOSICION_INTERNET,
            'estadosActivos' => Proyecto::ESTADOS_ACTIVOS,
            'estadosLabels' => Proyecto::ESTADOS_LABELS,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateProyecto(Request $request): array
    {
        return $request->validate([
            'nombre_proyecto' => ['required', 'string', 'max:255'],
            'nombre_proyecto_marca' => ['required', 'string', 'max:255'],
            'area_solicitante_id' => ['required', 'integer', 'exists:ministerio_secretarias,id'],
            // US-03: estado editable, solo los 4 activos (nunca archivado).
            'estado' => ['required', 'string', 'in:'.implode(',', Proyecto::ESTADOS_ACTIVOS)],
            'componentes' => ['required', 'array', 'min:1'],
            'componentes.*.nombre_componente' => ['required', 'string', 'max:255'],
            'componentes.*.tipo_componente' => ['required', 'string', 'in:'.implode(',', self::TIPO_COMPONENTE)],
            'componentes.*.tecnologia' => ['required', 'string', 'max:255'],
            'componentes.*.version' => ['required', 'string', 'max:255'],
            'componentes.*.exposicion_internet' => ['required', 'string', 'in:'.implode(',', self::EXPOSICION_INTERNET)],
            'componentes.*.observaciones' => ['nullable', 'string'],
            'componentes.*.repositorio_gitlab' => ['required', 'string', 'max:255'],
            'componentes.*.url_dev' => ['required', 'string', 'max:255'],
            'componentes.*.nombre_normalizado' => ['required', 'string', 'max:255'],
            'componentes.*.url_openshift' => ['required', 'string', 'max:255'],
        ], [
            'nombre_proyecto.required' => 'El nombre del proyecto es obligatorio.',
            'nombre_proyecto_marca.required' => 'El nombre de marca del proyecto es obligatorio.',
            'area_solicitante_id.required' => 'Debe seleccionar el área solicitante.',
            'area_solicitante_id.exists' => 'El área solicitante seleccionada no es válida.',
            'estado.required' => 'Debe seleccionar un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'componentes.required' => 'Debe cargar al menos un componente.',
            'componentes.min' => 'Debe cargar al menos un componente.',
            'componentes.*.nombre_componente.required' => 'El nombre del componente es obligatorio.',
            'componentes.*.tipo_componente.required' => 'El tipo de componente es obligatorio.',
            'componentes.*.tecnologia.required' => 'La tecnología es obligatoria.',
            'componentes.*.version.required' => 'La versión es obligatoria.',
            'componentes.*.exposicion_internet.required' => 'La exposición a Internet es obligatoria.',
            'componentes.*.repositorio_gitlab.required' => 'El repositorio GitLab es obligatorio.',
            'componentes.*.url_dev.required' => 'La URL de desarrollo es obligatoria.',
            'componentes.*.nombre_normalizado.required' => 'El nombre normalizado es obligatorio.',
            'componentes.*.url_openshift.required' => 'La URL de OpenShift es obligatoria.',
        ]);
    }

    /**
     * Filtra los datos de un componente del formulario hacia el fillable.
     *
     * @param  array<string, mixed>  $componente
     * @return array<string, mixed>
     */
    protected function componenteData(array $componente): array
    {
        return [
            'nombre_componente' => $componente['nombre_componente'],
            'tipo_componente' => $componente['tipo_componente'],
            'tecnologia' => $componente['tecnologia'],
            'version' => $componente['version'],
            'exposicion_internet' => $componente['exposicion_internet'],
            'observaciones' => $componente['observaciones'] ?? null,
            'repositorio_gitlab' => $componente['repositorio_gitlab'],
            'url_dev' => $componente['url_dev'],
            'nombre_normalizado' => $componente['nombre_normalizado'],
            'url_openshift' => $componente['url_openshift'],
        ];
    }

    /**
     * Aplica el filtro de trimestre (Q1..Q4) sobre el created_at.
     */
    protected function applyTrimestre($query, string $trimestre): void
    {
        // Se normaliza a string simple (puede llegar como Stringable desde $request->string()).
        $trimestre = (string) $trimestre;

        $trimestres = [
            'Q1' => [1, 3],
            'Q2' => [4, 6],
            'Q3' => [7, 9],
            'Q4' => [10, 12],
        ];

        if (! isset($trimestres[$trimestre])) {
            return;
        }

        [$desde, $hasta] = $trimestres[$trimestre];

        $query->whereBetween(DB::raw('MONTH(created_at)'), [$desde, $hasta]);
    }

    /**
     * Devuelve la etiqueta de repartición de un área solicitante.
     * Nivel3 si no tiene secretaría, o nivel3 · nivel4 cuando la tiene.
     */
    protected function reparticionLabel(?MinisterioSecretaria $area): string
    {
        if (! $area) {
            return 'Sin área';
        }

        if ($area->nivel4 !== null && $area->nivel4 !== '-' && $area->nivel3 !== '-') {
            return $area->nivel3.' · '.$area->nivel4;
        }

        return $area->nivel3 ?? $area->nivel2 ?? 'Sin área';
    }
}
