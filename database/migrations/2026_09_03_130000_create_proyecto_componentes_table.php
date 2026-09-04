<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')
                ->constrained('proyectos')
                ->cascadeOnDelete();
            $table->string('nombre_componente');
            $table->string('tipo_componente');
            $table->string('tecnologia');
            $table->string('version');
            $table->string('exposicion_internet');
            $table->text('observaciones')->nullable();
            $table->string('repositorio_gitlab');
            $table->string('url_dev');
            $table->string('nombre_normalizado');
            $table->string('url_openshift');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_componentes');
    }
};
