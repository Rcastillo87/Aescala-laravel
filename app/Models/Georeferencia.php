<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Georeferencia extends Model
{
    use HasFactory;
    protected $table = 'Georeferencia';

    protected $fillable = [
        'dispositivo_id',
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