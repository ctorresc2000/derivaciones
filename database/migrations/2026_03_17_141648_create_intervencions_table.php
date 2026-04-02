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
        Schema::create('intervencions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->foreignId('usuario_id')->constrained('users'); // El profesional que hizo la intervención
            $table->foreignId('via_ingreso_id')->constrained('viaingresos');

            $table->text('descripcion');
            $table->date('fecha');
            $table->enum('estado', ['Abierta','Derivada', 'Concluida'])->default('Abierta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervencions');
    }
};
