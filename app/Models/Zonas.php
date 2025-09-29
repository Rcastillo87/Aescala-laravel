<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zonas extends Model
{
    use HasFactory;

    protected $table = 'zonas';
    public $timestamps = false;

    protected $fillable = [
        'nombre_zona'
    ];

    public function zona_entregable()
    {
        return $this->hasMany(ZonaEntregable::class, 'id_zonas', 'id');
    }
}