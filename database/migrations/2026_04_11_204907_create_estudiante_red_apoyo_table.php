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
        Schema::create('estudiante_red_apoyo', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estudiante_id')->constrained()->onDelete('cascade');
        $table->foreignId('red_apoyo_id')->constrained('redes_apoyos')->onDelete('cascade');
        // Esto es oro para el psicólogo:
        $table->string('observacion')->nullable(); // Ej: "Atención con Dr. Arriagada"
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_red_apoyo');
    }
};
