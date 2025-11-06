<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The Attributes that are mass Assignable.
     *
     * @var list<string>
     */

protected $fillable = [
    'name', 'email', 'password', 'rol'
];
    /**
     * The Atributtes that should be hidden for Serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con docente (si es docente)
    public function docente()
    {
        return $this->hasOne(Docente::class);
    }

    // Relación con materias asignadas como docente
    public function materiasAsignadas()
    {
        return $this->hasMany(GrupoMateria::class, 'docente_id');
    }

    // Relación con horarios como docente
    public function horarios()
    {
        return $this->hasManyThrough(
            HorarioDocente::class,
            GrupoMateria::class,
            'docente_id', // Foreign key on GrupoMateria table
            'grupo_materia_id', // Foreign key on HorarioDocente table
            'id', // Local key on User table
            'id' // Local key on GrupoMateria table
        );
    }

    // Relación con bitácoras
    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }

    // Relación con asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'docente_id');
    }

    // Scope para usuarios docentes
    public function scopeDocentes($query)
    {
        return $query->where('rol', 'docente');
    }

    // Verificar si es admin
    public function isAdmin()
    {
        return $this->rol === 'admin';
    }

    // Verificar si es docente
    public function isDocente()
    {
        return $this->rol === 'docente';
    }
}