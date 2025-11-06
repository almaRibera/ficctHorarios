<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigla_grupo',
        'codigo_grupo'
    ];

    public function materiasAsignadas()
    {
        return $this->hasMany(GrupoMateria::class);
    }

    public function horarios()
    {
        return $this->hasManyThrough(
            HorarioDocente::class,
            GrupoMateria::class,
            'grupo_id', // Foreign key on GrupoMateria table
            'grupo_materia_id', // Foreign key on HorarioDocente table
            'id', // Local key on Grupo table
            'id' // Local key on GrupoMateria table
        );
    }

    // Obtener docentes únicos del grupo
    public function docentes()
    {
        return $this->hasManyThrough(
            User::class,
            GrupoMateria::class,
            'grupo_id', // Foreign key on GrupoMateria table
            'id', // Foreign key on User table
            'id', // Local key on Grupo table
            'docente_id' // Local key on GrupoMateria table
        )->distinct();
    }

    // Verificar si el grupo tiene horarios asignados
    public function tieneHorarios()
    {
        return $this->horarios()->exists();
    }
}