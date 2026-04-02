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
        Schema::create('archivosderivacions', function (Blueprint $table) {
            $table->id();

            // Relación con la derivación. cascadeOnDelete borra los archivos de la BD si se borra la derivación
            $table->foreignId('derivarestudiante_id')->constrained('derivarestudiantes')->cascadeOnDelete();

            // Dónde se guarda físicamente en el servidor
            $table->string('ruta_archivo');

            // El nombre original para cuando lo quieran descargar
            $table->string('nombre_original');

            $table->string('extension')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivosderivacions');
    }
};
