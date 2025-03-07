<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'id_user',
        'id_tarea_estado',
        'descripccion',
        'id_tarea_tipo',
        'fec_inicio',
        'fec_fin'
    ];

    protected $casts = [
        'fec_inicio' => 'datetime',
        'fec_fin' => 'datetime'
    ];

    // Relación con el modelo Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getNameTareaEstadoAttribute()
    {
         switch ($this->id_tarea_estado) {
             case 1:
                 return 'En Pausa';
                 break;
             case 2:
                 return 'En Progreso';
                 break;
             case 3:
                 return 'Finalizado';
                 break;
             default:
                 return 'No Definido';
         }
    }

    public function getClassTareaEstadoAttribute()
    {
        switch ($this->id_tarea_estado) {
            case 1:
                return 'bg-yellow-500 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                break;
            case 2:
                return 'bg-green-500 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                break;
            case 3:
                return 'bg-red-500 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                break;
            default:
                return 'bg-gray-300 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded ';
        }
    }

    // Relación con el modelo TareaTipo
    public function tareaTipo()
    {
        return $this->belongsTo(TareaTipo::class, 'id_tarea_tipo');
    }
}