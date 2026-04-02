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
        Schema::create('derivarestudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->date('fecha_derivacion');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('motivo_derivacion');
            $table->foreignId('profesional_derivado_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // Puedes usar ->nullOnDelete() si prefieres que no se borre la derivación si borras al profesional;
            $table->text('detalle_derivacion');
            $table->text('conclusiones')->nullable();
            $table->enum('estado',['Pendiente','En Proceso','Cerrado'])->default('Pendiente');
            $table->text('previos_derivacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derivarestudiantes');
    }
};
