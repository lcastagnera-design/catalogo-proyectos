@php
    // GOTCHA CRÍTICO: en create() no se pasa $proyecto. Este partial se comparte
    // entre create y edit, por eso se inicializa antes de acceder a $proyecto->algo.
    $proyecto ??= new \App\Models\Proyecto();

    $nombre_proyecto = $nombre_proyecto ?? $proyecto->nombre_proyecto ?? '';
    $nombre_proyecto_marca = $nombre_proyecto_marca ?? $proyecto->nombre_proyecto_marca ?? '';

    // US-03: estado editable. En create (proyecto nuevo) default 'planificacion'.
    // En edit, toma el estado del proyecto.
    $estado = $estado ?? ($proyecto->exists ? $proyecto->estado : 'planificacion');

    // Lista de estados activos para el select (excluye 'archivado').
    $estadosActivos = $estadosActivos ?? \App\Models\Proyecto::ESTADOS_ACTIVOS;
    $estadosLabels = $estadosLabels ?? \App\Models\Proyecto::ESTADOS_LABELS;

    // Área: área actual en edición, si existe.
    $areaActual = $proyecto->exists && $proyecto->areaSolicitante
        ? $proyecto->areaSolicitante
        : null;
@endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="nombre_proyecto" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del proyecto <span class="text-red-500">*</span></label>
            <input type="text" id="nombre_proyecto" name="nombre_proyecto" value="{{ old('nombre_proyecto', $nombre_proyecto) }}"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-gray-50 transition-colors @error('nombre_proyecto') border-red-300 bg-red-50 @enderror">
            @error('nombre_proyecto')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nombre_proyecto_marca" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre de marca <span class="text-red-500">*</span></label>
            <input type="text" id="nombre_proyecto_marca" name="nombre_proyecto_marca" value="{{ old('nombre_proyecto_marca', $nombre_proyecto_marca) }}"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-gray-50 transition-colors @error('nombre_proyecto_marca') border-red-300 bg-red-50 @enderror">
            @error('nombre_proyecto_marca')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Área solicitante: 2 selects anidados (nivel3 -> nivel4). El nivel2 es contexto. --}}
    <div class="border border-gray-200 rounded-2xl p-4 sm:p-5 bg-gray-50/50 space-y-4">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-gray-700">Área solicitante</h3>
            <span class="text-xs text-gray-400 font-medium">(Jefatura / Vicejefatura de Gobierno o Ministerios)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="nivel3" class="block text-xs font-semibold text-gray-600 mb-1">Ministerio / Área <span class="text-red-500">*</span></label>
                <select id="nivel3" name="nivel3"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors @error('area_solicitante_id') border-red-300 @enderror">
                    <option value="">Seleccionar ministerio / área…</option>
                    @foreach ($areasNivel3 as $nivel3)
                        <option value="{{ $nivel3 }}" {{ (old('nivel3', $areaActual?->nivel3) === $nivel3) ? 'selected' : '' }}>{{ $nivel3 }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nivel4" class="block text-xs font-semibold text-gray-600 mb-1">Secretaría / Dependencia <span class="text-red-500">*</span></label>
                <select id="nivel4" name="area_solicitante_id"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors @error('area_solicitante_id') border-red-300 bg-red-50 @enderror">
                    <option value="">Seleccioná primero el ministerio…</option>
                    @if ($areaActual)
                        <option value="{{ $areaActual->id }}" selected>
                            {{ $areaActual->nivel4 !== null && $areaActual->nivel4 !== '-' ? $areaActual->nivel4 : $areaActual->nivel3 }}
                        </option>
                    @endif
                </select>
                @error('area_solicitante_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- US-03: Estado del proyecto (solo los 4 activos, nunca archivado). --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="estado" class="block text-sm font-semibold text-gray-700 mb-1.5">Estado del proyecto <span class="text-red-500">*</span></label>
            <select id="estado" name="estado"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors @error('estado') border-red-300 bg-red-50 @enderror">
                @foreach ($estadosActivos as $estadoValor)
                    <option value="{{ $estadoValor }}" {{ old('estado', $estado) === $estadoValor ? 'selected' : '' }}>
                        {{ $estadosLabels[$estadoValor] ?? ucfirst($estadoValor) }}
                    </option>
                @endforeach
            </select>
            @error('estado')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Componentes 1 a N: filas dinámicas --}}
    <div class="border border-gray-200 rounded-2xl p-4 sm:p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-700">Componentes</h3>
                <p class="text-xs text-gray-400">Uno o más componentes por proyecto.</p>
            </div>
            <button type="button" id="agregar-componente"
                class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar componente
            </button>
        </div>

        <div id="componentes-container" class="space-y-4">
            @php $componenteIndex = 0; @endphp
            @forelse ($proyecto->componentes ?? [] as $componente)
                @include('proyectos.partials._componente_row', ['idx' => $componenteIndex, 'componente' => $componente])
                @php $componenteIndex++; @endphp
            @empty
                {{-- En create (sin componentes) se agrega una fila vacía de arranque. --}}
                @include('proyectos.partials._componente_row', ['idx' => 0])
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('componentes-container');
            const addBtn = document.getElementById('agregar-componente');
            const nivel3 = document.getElementById('nivel3');
            const nivel4 = document.getElementById('nivel4');
            const areasRoute = @json(route('areas'));

            // --- Componentes dinámicos: acordeón + agregar/quitar + renumerar ---
            if (container) {
                const expandir = function (row, abierto) {
                    const cuerpo = row.querySelector('.componente-cuerpo');
                    const chevron = row.querySelector('.componente-chevron');
                    if (!cuerpo) return;
                    if (abierto) {
                        cuerpo.classList.remove('hidden');
                        if (chevron) chevron.classList.remove('-rotate-180');
                    } else {
                        cuerpo.classList.add('hidden');
                        if (chevron) chevron.classList.add('-rotate-180');
                    }
                };

                const actualizarResumen = function (row) {
                    const resumen = row.querySelector('.componente-resumen');
                    if (resumen) {
                        const nombre = row.querySelector('[name$="[nombre_componente]"]')?.value || '‐';
                        const tipo = row.querySelector('[name$="[tipo_componente]"]')?.value || '‐';
                        const tec = row.querySelector('[name$="[tecnologia]"]')?.value || '‐';
                        const ver = row.querySelector('[name$="[version]"]')?.value || '‐';
                        resumen.textContent = nombre + ' · ' + tipo + ' · ' + tec + ' · ' + ver;
                    }
                };

                const reindex = function () {
                    const rows = container.querySelectorAll('.componente-row');
                    rows.forEach(function (row, i) {
                        // Quitar el botón "Quitar" de la primera fila.
                        row.querySelectorAll('.quitar-componente').forEach(function (btn) {
                            btn.style.display = i === 0 ? 'none' : '';
                        });
                        const idx = row.querySelector('.componente-index');
                        if (idx) idx.textContent = i + 1;
                        // Renombrar name de cada input/select/textarea: componentes[i][campo]
                        row.querySelectorAll('[name^="componentes["]').forEach(function (el) {
                            const field = el.name.replace(/componentes\[\d+\]\[([^\]]+)\]/, '$1');
                            el.name = `componentes[${i}][${field}]`;
                        });
                        actualizarResumen(row);
                    });
                };

                const addRow = function () {
                    const rows = container.querySelectorAll('.componente-row');
                    const row = rows[rows.length - 1]?.cloneNode(true) ?? null;
                    if (!row) return;
                    // Vaciar valores.
                    row.querySelectorAll('input, textarea').forEach(function (el) {
                        if (el.type !== 'button') el.value = '';
                    });
                    row.querySelectorAll('select').forEach(function (el) {
                        el.selectedIndex = 0;
                    });
                    // Colapsar la fila anterior (la que se clonó) y dejar la nueva expandida.
                    if (rows.length) expandir(rows[rows.length - 1], false);
                    expandir(row, true);
                    // Asegurar botón quitar.
                    if (!row.querySelector('.quitar-componente')) {
                        const head = row.querySelector('.componente-cabezal .flex.items-center');
                        if (head) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'quitar-componente inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg';
                            btn.textContent = 'Quitar';
                            head.appendChild(btn);
                        }
                    }
                    container.appendChild(row);
                    reindex();
                    const nombreInput = row.querySelector('[name$="[nombre_componente]"]');
                    if (nombreInput) nombreInput.focus();
                };

                if (addBtn) addBtn.addEventListener('click', addRow);

                // Delegación de eventos en el contenedor: quitar, toggle de cabezal y resumen en vivo.
                container.addEventListener('click', function (e) {
                    const quitarBtn = e.target.closest('.quitar-componente');
                    if (quitarBtn) {
                        quitarBtn.closest('.componente-row').remove();
                        reindex();
                        return;
                    }
                    const cabezal = e.target.closest('.componente-cabezal');
                    if (cabezal) {
                        const row = cabezal.closest('.componente-row');
                        const cuerpo = row.querySelector('.componente-cuerpo');
                        expandir(row, cuerpo.classList.contains('hidden'));
                    }
                });
                container.addEventListener('input', function (e) {
                    const el = e.target;
                    if (el.matches('[name$="[nombre_componente]"], [name$="[tipo_componente]"], [name$="[tecnologia]"], [name$="[version]"]')) {
                        actualizarResumen(el.closest('.componente-row'));
                    }
                });
                container.addEventListener('change', function (e) {
                    const el = e.target;
                    if (el.matches('select[name$="[tipo_componente]"], select[name$="[tecnologia]"], select[name$="[version]"]')) {
                        actualizarResumen(el.closest('.componente-row'));
                    }
                });

                // Estado inicial: primera fila expandida, el resto (edit) colapsadas.
                container.querySelectorAll('.componente-row').forEach(function (row, i) {
                    expandir(row, i === 0);
                });

                reindex();
            }

            // --- Select anidado de área: nivel3 filtra nivel4 vía AJAX ---
            if (nivel3 && nivel4) {
                const cargarNivel4 = function (nivel3Val, keepSelection) {
                    if (!nivel3Val) {
                        nivel4.innerHTML = '<option value="">Seleccioná primero el ministerio…</option>';
                        return;
                    }
                    fetch(areasRoute + '?nivel3=' + encodeURIComponent(nivel3Val), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                        .then(function (res) { return res.json(); })
                        .then(function (options) {
                            let html = '<option value="">Seleccionar secretaría / dependencia…</option>';
                            options.forEach(function (opt) {
                                html += '<option value="' + opt.id + '">' + opt.label + '</option>';
                            });
                            nivel4.innerHTML = html;
                            if (keepSelection) nivel4.value = keepSelection;
                        })
                        .catch(function () {
                            nivel4.innerHTML = '<option value="">Error al cargar…</option>';
                        });
                };

                nivel3.addEventListener('change', function () {
                    cargarNivel4(this.value, null);
                });

                // Si hay un área pre-cargada (edición), reportar sus opciones.
                const areaPreseleccionada = @json($areaActual?->id);
                if (nivel3.value && areaPreseleccionada) {
                    cargarNivel4(nivel3.value, areaPreseleccionada);
                }
            }
        });
    </script>
</div>
