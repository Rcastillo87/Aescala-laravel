<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigAdicionales extends Model
{
    use HasFactory;

    protected $table = 'config_adicionales_mo';
    public $timestamps = true;

    protected $fillable = [
        'producto',
        'valor_unidad',
        'año',
        'tipo'
    ];
}