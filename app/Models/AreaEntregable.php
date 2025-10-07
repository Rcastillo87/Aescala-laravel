<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaEntregable extends Model
{
    use HasFactory;

    protected $table = 'area_entregables';
    protected $primaryKey = null; 
    public $incrementing = false; 
    public $timestamps = false;

    protected $fillable = [
        'id_area',
        'entregable'
    ];
}