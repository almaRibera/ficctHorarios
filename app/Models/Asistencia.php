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
        'fecha',
        'hora_registro',
        'estado',
        'foto_evidencia',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
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

    // Scope para asistencias de hoy
    public function scopeHoy($query)
    {
        return $query->where('fecha', today());
    }

    // Verificar si ya se registró asistencia para este horario hoy
    public static function yaRegistrado($horarioDocenteId)
    {
        return static::where('horario_docente_id', $horarioDocenteId)
            ->where('fecha', today())
            ->exists();
    }
}