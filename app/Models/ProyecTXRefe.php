<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyecTXRefe extends Model
{
    use HasFactory;

    protected $table = 'proyec_tx_refe';
    public $timestamps = false;

    protected $fillable = [
        'tx_descripcion',
        'referencia',
        'id_proyecto'
    ];
}