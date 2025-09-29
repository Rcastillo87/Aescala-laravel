<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaEntregable extends Model
{
    use HasFactory;

    protected $table = 'zona_entregables';
    protected $primaryKey = null; 
    public $incrementing = false; 
    public $timestamps = false;

    protected $fillable = [
        'id_zonas',
        'entregable'
    ];
}