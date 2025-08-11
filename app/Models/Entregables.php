<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entregables extends Model
{
    use HasFactory;

    protected $table = 'entregables';
    public $timestamps = false;

    protected $fillable = [
        'nombre_estregable'
    ];

    // Relación con el modelo Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}