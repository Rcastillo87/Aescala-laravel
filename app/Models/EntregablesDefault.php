<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregablesDefault extends Model
{
    use HasFactory;

    protected $table = 'entregable_default';
    public $timestamps = false;

    protected $fillable = [
        'id_estregable',
        'descripccion'
    ];
    
}