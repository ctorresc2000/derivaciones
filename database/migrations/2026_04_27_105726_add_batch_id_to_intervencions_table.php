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
        Schema::table('intervencions', function (Blueprint $table) {
            // Añadimos el identificador de grupo
            $table->uuid('batch_id')->nullable()->after('estado');
            // Si no usas via_ingreso para definir el tipo, podemos añadir el campo 'tipo'
            $table->string('tipo_intervencion')->nullable()->after('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervencions', function (Blueprint $table) {
            //
        });
    }
};
