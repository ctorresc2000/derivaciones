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
        Schema::create('apoderado_estudiante', function (Blueprint $table) {
            $table->id();
            // Claves foráneas que conectan ambas tablas
            $table->foreignId('estudiante_id')->constrained()->onDelete('cascade');
            $table->foreignId('apoderado_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apoderado_estudiante');
    }
};
