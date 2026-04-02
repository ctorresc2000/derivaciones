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
        Schema::create('accion_intervencions', function (Blueprint $table) {
        $table->id();
        // Relación con la tabla principal
        $table->foreignId('intervencion_id')->constrained('intervencions')->onDelete('cascade');
        // Quien registra la acción
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('fecha');
        $table->text('descripcion');
        $table->timestamps();
    });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accion_intervencions');
    }
};
