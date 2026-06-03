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
        'fecha_pago_cli'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

}
