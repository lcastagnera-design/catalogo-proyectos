<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    /** Estados activos editables del proyecto (no incluye archivado). */
    public const ESTADOS_ACTIVOS = [
        'planificacion',
        'ejecucion',
        'frenado',
        'finalizado',
    ];

    /** Estado de baja lógica (archivado). No es un estado activo editable. */
    public const ESTADO_ARCHIVADO = 'archivado';

    /** Etiquetas legibles para cada estado. */
    public const ESTADOS_LABELS = [
        'planificacion' => 'Planificación',
        'ejecucion' => 'Ejecución',
        'frenado' => 'Frenado',
        'finalizado' => 'Finalizado',
        'archivado' => 'Archivado',
    ];

    /** @var list<string> */
    protected $fillable = [
        'nombre_proyecto',
        'nombre_proyecto_marca',
        'area_solicitante_id',
        'estado',
        'url_sharepoint',
    ];

    /**
     * Área solicitante (ministerio/secretaría del 3º y 4º nivel).
     */
    public function areaSolicitante(): BelongsTo
    {
        return $this->belongsTo(MinisterioSecretaria::class, 'area_solicitante_id');
    }

    /**
     * Componentes del proyecto (relación uno a muchos).
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(ProyectoComponente::class);
    }

    /**
     * Scope: excluye los proyectos archivados (baja lógica) por defecto.
     */
    public function scopeNoArchivados($query)
    {
        return $query->where('estado', '!=', self::ESTADO_ARCHIVADO);
    }

    /**
     * Scope: incluye (o no) los proyectos archivados.
     */
    public function scopeIncluyendoArchivados($query, bool $incluir = false)
    {
        return $incluir ? $query : $query->where('estado', '!=', self::ESTADO_ARCHIVADO);
    }

    /**
     * Etiqueta legible del estado (ej. para badges y selects).
     */
    public function estadoLabel(): string
    {
        return self::ESTADOS_LABELS[$this->estado] ?? ucfirst((string) $this->estado);
    }
}
