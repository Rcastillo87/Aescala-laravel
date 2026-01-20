<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorArea extends Model
{
    use HasFactory;

    protected $table = 'valor_area';
    public $timestamps = true;

    protected $fillable = [
        'descripccion',
        'valor_min',
        'valor_max',
        'valor',
        'año'
    ];
}