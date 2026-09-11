<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Novedades extends Model
{
    use HasFactory;

    protected $table = 'proyecto_novedades';

    const CREATED_AT = 'createdAt';                                                                                                                                                                                                         
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'novedades',
        'id_proyecto',
        'id_user',
        'fecha_respuesta',
        'estado',
        'comentario'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public static $estado = [
        1 => 'Notificado',
        2 => 'En Revisión',
        3 => 'Aceptado',
        4 => 'Cancelado',
        5 => 'Parcial'

    ];
    public static $ClassEstado = [
        1 => 'span-blue',
        2 => 'span-yellow',
        3 => 'span-green',
        4 => 'span-red',
        5 => 'span-orange'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estado[$this->estado] ?? 'Desconocido') . '</span>';
    }

}