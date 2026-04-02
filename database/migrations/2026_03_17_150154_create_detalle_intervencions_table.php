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
        Schema::create('detalle_intervencions', function (Blueprint $table) {
            $table->id();
            // Si se borra la intervención, se borran sus detalles (cascade)
            $table->foreignId('intervencion_id')->constrained('intervencions')->onDelete('cascade');

            $table->foreignId('falta_id')->constrained('faltas');
            $table->foreignId('medida_id')->constrained('medidas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_intervencions');
    }
};
