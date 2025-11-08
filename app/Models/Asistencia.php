<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'horario_docente_id',
        'docente_id',
        'fecha_clase',
        'hora_registro',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_clase' => 'date',
        'hora_registro' => 'datetime:H:i',
    ];

    public function horario()
    {
        return $this->belongsTo(HorarioDocente::class, 'horario_docente_id');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    // Scope para asistencias de un docente
    public function scopeDocente($query, $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    // Scope para asistencias por fecha
    public function scopeFecha($query, $fecha)
    {
        return $query->where('fecha_clase', $fecha);
    }
}