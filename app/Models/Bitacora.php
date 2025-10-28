<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accion_realizada',
        'fecha_y_hora'
    ];

    protected $casts = [
        'fecha_y_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}