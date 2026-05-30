<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaTipo extends Model
{
    use HasFactory;

    protected $table = 'tarea_tipos';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tarea',
        'porcentage',
        'orden',
        'dias_default'
    ];

    // Relación con el modelo Tarea
    public function tareas()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea_tipo');
    }
}
