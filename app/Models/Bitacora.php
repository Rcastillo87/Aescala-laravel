<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'id_user',
        'servicio',
        'metodo',
        'url',
        'ip_address',
        'user_agent',
        'payload',
        'error',
        'tipo',
        'status_code',
        'duracion_ms',
    ];

    protected $casts = [
        'payload' => 'array',
        'error'   => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
