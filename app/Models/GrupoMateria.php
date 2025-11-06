<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function horasAsignadas()
    {
        return $this->horarios()->sum(DB::raw('EXTRACT(EPOCH FROM (hora_fin - hora_inicio))')) / 3600;
    }

    public function horasPendientes()
    {
        return $this->horas_semanales - $this->horasAsignadas();
    }
}