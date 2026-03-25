<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'nombre_almacen',
        'id_user',
        'tipo'
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$classTipo[(int)$this->tipo] ?? 'default-class').'">'
        . (self::$tipo[(int)$this->tipo] ?? 'Desconocido') . '</span>';
    }

    public static $classTipo = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public static $tipo = [
        1 => 'Obra Blanca',
        2 => 'Carpinteria'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }


}
