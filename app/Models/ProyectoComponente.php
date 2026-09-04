<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyectoComponente extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'proyecto_id',
        'nombre_componente',
        'tipo_componente',
        'tecnologia',
        'version',
        'exposicion_internet',
        'observaciones',
        'repositorio_gitlab',
        'url_dev',
        'nombre_normalizado',
        'url_openshift',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }
}
