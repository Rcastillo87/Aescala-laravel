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
        'device',
        'id_user'
    ];

    public function georreferenciaciones()
    {
        return $this->hasMany(Georreferencia::class);
    }

    public function userAsignado()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}