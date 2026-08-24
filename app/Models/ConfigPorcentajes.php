<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPorcentajes extends Model
{
    use HasFactory;

    protected $table = 'config_porcentajes';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'concepto',
        'porcentage',
        'año',
        'descripccion',
        'en_pesos'
    ];
}