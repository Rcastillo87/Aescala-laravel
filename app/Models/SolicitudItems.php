<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudItems extends Model
{
    use HasFactory;

    protected $table = 'solicitud_items';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'id_material',
        'cantidad',
        'estado'
    ];

    public static $estados = [
        1 => 'Nuevo',
        2 => 'Despacho Parcial',
        3 => 'Despachado',
        4 => 'Cancelado'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudMaterial::class, 'id_solicitud');
    }
    
}