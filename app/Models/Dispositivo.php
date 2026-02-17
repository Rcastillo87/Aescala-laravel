<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use HasFactory;

    protected $table = 'dispositivo';

    protected $fillable = [
        'device_serial',
        'manufacturer',
        'model',
        'brand',
        'device'
    ];

    public function georreferenciaciones()
    {
        return $this->hasMany(Georeferencia::class);
    }
}