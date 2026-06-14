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
        Schema::table('apoderados', function (Blueprint $table) {
            // Agrega la columna password después del correo
            $table->string('password')->nullable()->after('correo');
        });
    }

    public function down(): void
    {
        Schema::table('apoderados', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
