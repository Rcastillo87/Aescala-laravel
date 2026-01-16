<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;

    protected $table = 'fases';
    public $timestamps = false;

    protected $fillable = [
        'fase',
        'id_materiales'
    ];
}