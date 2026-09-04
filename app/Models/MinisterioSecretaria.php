<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinisterioSecretaria extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'nivel2',
        'nivel3',
        'nivel4',
    ];

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'area_solicitante_id');
    }
}
