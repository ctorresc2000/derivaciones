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
        Schema::create('entrevistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained();
            $table->foreignId('curso_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Quién entrevista
            $table->boolean('es_apoderado')->default(false);
            $table->string('nombre_apoderado')->nullable();
            $table->enum('motivo', ['Solicitud apoderado', 'Solicitud Estudiante', 'Conductual', 'Asistencia', 'Atrasos']);
            $table->text('detalle');
            $table->date('fecha');
            $table->longText('firma')->nullable(); // Aquí guardamos el dibujo
            $table->string('otp_email_verified')->nullable();
            $table->string('otp_codigo')->nullable();
            $table->string('otp_email')->nullable(); // Correo al que se envió
            $table->timestamp('otp_verified_at')->nullable(); // Cuándo se validó
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrevistas');
    }
};
