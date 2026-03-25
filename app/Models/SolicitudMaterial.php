<?php

namespace App\Models;

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
        4 => 'Cotizacion'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-orange'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estados[$this->estado] ?? 'Desconocido') . '</span>';
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(SolicitudItems::class, 'id_solicitud');
    }

    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class, 'id_solicitud', 'id');
    }

    public function despachado()
    {
        return $this->hasMany(Despachos::class, 'id_solicitud', 'id');
    }

    public function getTotalItemsSolicitudAttribute()
    {
        return $this->items()->whereNotIn('estado', [3, 4])->sum('cantidad');
    }

    public function getAprobarItemsAttribute()
    {
        return $this->items()->where('aprobado', 0)->exists();
    }

    public function getTotalItemsEntregadoAttribute()
    {
        return $this->despachado()->sum('cantidad');
    }

    public function getEstadoItemsAttribute()
    {
        $arr = [];
        foreach ($this->items as $item) {
            $arr = array_merge($arr, $item->arrEstados);
        }
        $arr = array_values(array_unique($arr));

        return implode('', $arr);
    }

}
