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
        Schema::create('estudiante_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained()->onDelete('cascade');
            $table->foreignId('curso_id')->constrained()->onDelete('cascade');
            $table->integer('ano_academico');
            $table->enum('condicion', ['Repitente', 'Promovido','Egresaddo'])->default('Promovido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_cursos');
    }
};
