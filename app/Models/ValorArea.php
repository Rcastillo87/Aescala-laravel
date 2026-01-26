<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorArea extends Model
{
    use HasFactory;

    protected $table = 'valor_area';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'descripccion',
        'area_min',
        'area_max',
        'valor_intervalo',
        'año'
    ];
}