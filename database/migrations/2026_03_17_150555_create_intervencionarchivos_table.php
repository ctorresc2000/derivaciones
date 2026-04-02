<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intervencionarchivos', function (Blueprint $table) {
            $table->id();

            // Si se borra la intervención, se borran sus registros de archivos
            $table->foreignId('intervencion_id')->constrained('intervencions')->onDelete('cascade');

            // Datos del archivo
            $table->string('ruta');
            $table->string('nombre_original');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervencionarchivos');
    }
};
