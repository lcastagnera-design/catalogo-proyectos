<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo `url_sharepoint` (opcional) a la tabla proyectos.
     *
     * Es un enlace de referencia al espacio SharePoint del proyecto; al ser
     * opcional no bloquea el alta.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('url_sharepoint')
                ->nullable()
                ->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('url_sharepoint');
        });
    }
};
