<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    public $timestamps = false;

    protected $fillable = [
        'nombre_area'
    ];

    public function zona_entregable()
    {
        return $this->hasMany(AreaEntregable::class, 'id_area', 'id');
    }
}