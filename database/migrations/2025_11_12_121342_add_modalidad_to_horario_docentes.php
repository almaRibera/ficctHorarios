<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horario_docentes', function (Blueprint $table) {
            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial')->after('hora_fin');
            $table->string('enlace_virtual')->nullable()->after('modalidad');
        });
    }

    public function down(): void
    {
        Schema::table('horario_docentes', function (Blueprint $table) {
            $table->dropColumn(['modalidad', 'enlace_virtual']);
        });
    }
};