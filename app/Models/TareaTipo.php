<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaTipo extends Model
{
    use HasFactory;

    protected $table = 'tarea_tipos';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_tarea'
    ];

    // Relación con el modelo Tarea
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_tarea_tipo');
    }
}