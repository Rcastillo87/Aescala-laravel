<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otrosi extends Model
{
    use HasFactory;

    protected $table = 'otro_si';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_user_encargado',
        'numero',
        'fecha_creacion'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function user_encargado()
    {
        return $this->belongsTo(User::class, 'id_user_encargado', 'id');
    }

    public function area_entregable()
    {
        return $this->hasMany(AreaEntregable::class, 'id_otro_si');
    }

}
