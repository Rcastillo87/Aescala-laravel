<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CobroRefe extends Model
{
    use HasFactory;
    protected $table = 'cobro_refe';

    protected $fillable = [
        'id_proyecto',
        'referencia',
        'valor_pendiente',
        'id_user',
        'fecha_notificacion',
        'fecha_acuerdo_pago',
        'fecha_pago_cli',
        'estado'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function getDiasMoraAttribute()
    {
        if (!$this->fecha_acuerdo_pago) {
            return null;
        }

        $fechaAcuerdo = \Carbon\Carbon::parse($this->fecha_acuerdo_pago)->startOfDay();
        $fecha = \Carbon\Carbon::now()->startOfDay();

        return (int) $fecha->diffInDays($fechaAcuerdo, false);
    }

    public static $estado = [
        1 => 'Pendiente',
        2 => 'Pagado',
    ];

    public static $ClassEstado = [
        1 => 'span-red',
        2 => 'span-green'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estado[$this->estado] ?? 'Desconocido') . '</span>';
    }

}
