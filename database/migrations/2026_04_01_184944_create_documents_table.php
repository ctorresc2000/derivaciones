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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // LA MAGIA: Esto crea dos columnas: 'documentable_type' y 'documentable_id'
            $table->morphs('documentable');

            $table->string('name');           // Nombre original (ej: certificado_medico.pdf)
            $table->string('file_path');      // Ruta interna en el servidor
            $table->string('mime_type')->nullable(); // Tipo de archivo (application/pdf, image/jpeg)
            $table->unsignedBigInteger('size')->nullable(); // Peso del archivo en bytes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
