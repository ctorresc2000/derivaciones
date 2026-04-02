<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivosestudiantes', function (Blueprint $table) {
            $table->id();

            // Relación directa con el estudiante
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();

            $table->string('ruta_archivo');
            $table->string('nombre_original');
            $table->string('extension')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivosestudiantes');
    }
};
