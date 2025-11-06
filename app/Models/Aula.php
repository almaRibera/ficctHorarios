<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'piso',
        'tipo',
        'estado',
        'capacidad',
        'equipamiento',
        'observaciones'
    ];

    protected $casts = [
        'capacidad' => 'integer',
    ];

    // Scope para aulas disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    // Scope para aulas por tipo
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope para aulas por piso
    public function scopePiso($query, $piso)
    {
        return $query->where('piso', $piso);
    }

    // Método para verificar si está disponible
    public function estaDisponible()
    {
        return $this->estado === 'disponible';
    }

    // Método para obtener el nombre completo del tipo
    public function getTipoCompletoAttribute()
    {
        return $this->tipo === 'teorica' ? 'Teórica' : 'Laboratorio';
    }

    // Método para obtener el estado en formato legible
    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'disponible' => 'Disponible',
            'ocupada' => 'Ocupada',
            'mantenimiento' => 'En Mantenimiento',
            default => 'Desconocido'
        };
    }
}