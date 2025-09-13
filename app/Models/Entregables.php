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

    public function defaults()
    {
        return $this->hasMany(EntregablesDefault::class, 'id_estregable', 'id');
    }
}