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
        Schema::create('accion_derivacions', function (Blueprint $table) {
        $table->id();

        // OJO: Verifica que 'derivarestudiantes' sea el nombre real de tu tabla de derivaciones en la base de datos
        $table->foreignId('derivarestudiante_id')->constrained('derivarestudiantes')->onDelete('cascade');

        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('fecha');
        $table->text('descripcion');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accion_derivacions');
    }
};
