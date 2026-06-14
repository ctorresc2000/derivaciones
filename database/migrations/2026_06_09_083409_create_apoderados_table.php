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
        Schema::create('apoderados', function (Blueprint $table) {
            $table->id();
           // $table->foreignId('estudiante_id')->constrained()->onDelete('cascade');
            $table->string('apoderado');
            $table->string('rut');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('correo');
            $table->enum('estado',['Activo','Inactivo']);
            $table->string('carnet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apoderados');
    }
};
