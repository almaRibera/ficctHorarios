<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    use HasFactory;

    protected $table = 'grupo_materia';

    protected $fillable = [
        'grupo_id',
        'materia_id',
        'docente_id',
        'horas_semanales'
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioDocente::class, 'grupo_materia_id');
    }

    // Calcular horas asignadas
    public function horasAsignadas()
    {
        $totalSegundos = 0;
        foreach ($this->horarios as $horario) {
            $inicio = \Carbon\Carbon::parse($horario->hora_inicio);
            $fin = \Carbon\Carbon::parse($horario->hora_fin);
            $totalSegundos += $fin->diffInSeconds($inicio);
        }
        return $totalSegundos / 3600; // Convertir a horas
    }

    // Calcular horas pendientes
    public function horasPendientes()
    {
        return $this->horas_semanales - $this->horasAsignadas();
    }

    // Verificar si tiene horarios completos
    public function tieneHorariosCompletos()
    {
        return $this->horasPendientes() <= 0;
    }
}