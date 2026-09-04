<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministerio_secretarias', function (Blueprint $table) {
            $table->id();
            $table->string('nivel2');
            $table->string('nivel3');
            $table->string('nivel4')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministerio_secretarias');
    }
};
