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
}