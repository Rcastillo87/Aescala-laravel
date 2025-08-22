<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntregableProye extends Model
{
    use HasFactory;

    protected $table = 'entregable_proyecto';
    
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_entregable',
        'id_proyecto',
        'cantidad',
        'valor_total',
        'tx_entregable'
    ];
}
