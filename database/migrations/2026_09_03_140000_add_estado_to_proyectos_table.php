<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo unificado `estado` a la tabla proyectos.
     *
     * Valores activos: planificacion, ejecucion, frenado, finalizado.
     * Valor de baja lógica: archivado (NO borra el registro, solo lo oculta).
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('estado', 20)
                ->default('planificacion')
                ->index()
                ->after('area_solicitante_id');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
