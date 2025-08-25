<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntregableProye extends Model
{
    use HasFactory;

    protected $table = 'entregable_proyecto';
    protected $primaryKey = null; 
    public $incrementing = false; 
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_entregable',
        'id_proyecto',
        'cantidad',
        'valor_total',
        'tx_entregable',
    ];

    // Relación con el modelo Herramienta
    public function entregable()
    {
        return $this->belongsTo(Entregables::class, 'id_entregable');
    }
}
