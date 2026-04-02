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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('social')->nullable();
            $table->string('rut')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado')->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
