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
        'id_user',
        'nombre_equipo',
        'imei_1',
        'imei_2'
    ];

    public function georreferenciaciones()
    {
        return $this->hasMany(Georreferencia::class);
    }

    public function userAsignado()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ultimaUbicacion()
    {
        return $this->hasOne(
            Georreferencia::class,
            'device_id', // FK real
            'id'
        )->whereDate('created_at', today())->latestOfMany('created_at');
    }

}
