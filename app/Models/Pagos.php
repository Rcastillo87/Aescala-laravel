<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;
    protected $table = 'pagos';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'id_user',
        'fecha_pago',
        'comentario',
        'rc'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    // app/Models/Pagos.php
    public function soporte()
    {
        return $this->morphOne(Documento::class, 'documentable');
    }

    public function pago_refe()
    {
        return $this->hasMany(PagoRefe::class, 'id_pago', 'id');
    }

    public function getValorTotalAttribute(){
        return $this->pago_refe()->sum('valor');
    }

}
