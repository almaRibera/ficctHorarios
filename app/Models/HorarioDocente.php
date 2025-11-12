<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioDocente extends Model
{
    use HasFactory;

    protected $table = 'horario_docentes';

    protected $fillable = [
        'grupo_materia_id',
        'aula_id',
        'dia',
        'hora_inicio',
        'hora_fin',
        'modalidad',
        'enlace_virtual'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function grupoMateria()
    {
        return $this->belongsTo(GrupoMateria::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    // Accesor para modalidad en formato legible
    public function getModalidadTextoAttribute()
    {
        return $this->modalidad === 'presencial' ? 'Presencial' : 'Virtual';
    }

    // Scope para horarios presenciales
    public function scopePresencial($query)
    {
        return $query->where('modalidad', 'presencial');
    }

    // Scope para horarios virtuales
    public function scopeVirtual($query)
    {
        return $query->where('modalidad', 'virtual');
    }

    // Verificar si es presencial
    public function esPresencial()
    {
        return $this->modalidad === 'presencial';
    }

    // Verificar si es virtual
    public function esVirtual()
    {
        return $this->modalidad === 'virtual';
    }

    // Accesor para el icono de modalidad
    public function getIconoModalidadAttribute()
    {
        return $this->esPresencial() ? '🏫' : '💻';
    }

    // Accesor para el color de modalidad
    public function getColorModalidadAttribute()
    {
        return $this->esPresencial() ? 'blue' : 'green';
    }
}