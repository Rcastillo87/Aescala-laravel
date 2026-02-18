<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Georreferencia extends Model
{
    use HasFactory;
    protected $table = 'georreferencias';

    protected $fillable = [
        'device_id',
        'lat',
        'lng',
        'accuracy',
        'speed',
        'battery',
        'request_at'
    ];

    protected $casts = [
        'request_at' => 'datetime'
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }
}