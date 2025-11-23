<?php

namespace App\Models;

use App\Http\Controllers\MaterialController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudMaterial extends Model
{
    use HasFactory;

    protected $table = 'solicitud_material';
    public $timestamps = false;

    protected $fillable = [
        'fecha_solicitud',
        'id_proyecto',
        'id_user',
        'estado',
        'observacion'
    ];

    public static $estados = [
        1 => 'Nuevo',
        2 => 'Despacho Parcial',
        3 => 'Entregado',
        4 => 'Cancelado'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-red'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estados[$this->estado] ?? 'Desconocido') . '</span>';
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(SolicitudItems::class, 'id_solicitud', 'id');
    }

    public function despachado()
    {
        return $this->hasMany(InventarioSolicitud::class, 'id_solicitud', 'id');
    }

    public function getTotalItemsSolicitudAttribute()
    {
        return $this->items()->sum('cantidad');
    }

    public function getTotalItemsEntregadoAttribute()
    {
        return $this->despachado()->sum('cantidad');
    }   
    
}