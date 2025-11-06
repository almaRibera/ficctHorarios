<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigla',
        'nombre',
        'nivel',
        'tipo'
    ];

    protected $casts = [
        'nivel' => 'integer',
    ];

    // Relación con grupos_materia (tabla pivote)
    public function gruposMateria()
    {
        return $this->hasMany(GrupoMateria::class);
    }

    // Relación con grupos a través de la tabla pivote
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_materia', 'materia_id', 'grupo_id')
                    ->withTimestamps();
    }

    // Relación con docentes a través de la tabla pivote
    public function docentes()
    {
        return $this->belongsToMany(User::class, 'grupo_materia', 'materia_id', 'docente_id')
                    ->withTimestamps();
    }

    // Scope para materias por nivel
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    // Scope para materias por tipo
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Método para obtener el tipo en formato legible
    public function getTipoCompletoAttribute()
    {
        return $this->tipo === 'truncal' ? 'Troncal' : 'Electiva';
    }

    // Contar cantidad de grupos asignados
    public function getCantidadGruposAttribute()
    {
        return $this->gruposMateria->count();
    }

    // Contar cantidad de docentes asignados
    public function getCantidadDocentesAttribute()
    {
        return $this->gruposMateria->unique('docente_id')->count();
    }
}