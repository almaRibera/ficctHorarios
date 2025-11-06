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
        return $this->hasManyThrough(HorarioDocente::class, GrupoMateria::class, 'grupo_id', 'grupo_materia_id');
    }
}