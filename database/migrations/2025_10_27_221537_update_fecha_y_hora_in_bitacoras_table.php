<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si necesitas cambiar el tipo de dato
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->timestamp('fecha_y_hora')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->string('fecha_y_hora')->change();
        });
    }
};