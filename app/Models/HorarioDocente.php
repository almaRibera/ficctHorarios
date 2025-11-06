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
        'hora_fin'
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
}