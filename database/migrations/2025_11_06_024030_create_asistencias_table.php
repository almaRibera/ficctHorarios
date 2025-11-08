<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_docente_id')->constrained()->onDelete('cascade');
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_clase');
            $table->time('hora_registro');
            $table->enum('estado', ['presente', 'tardanza', 'falta'])->default('presente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->unique(['horario_docente_id', 'fecha_clase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};