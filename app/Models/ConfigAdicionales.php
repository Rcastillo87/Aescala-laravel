<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigAdicionales extends Model
{
    use HasFactory;

    protected $table = 'config_adicionales_mo';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'producto',
        'valor_unidad',
        'año',
        'tipo',
        'descripccion'
    ];

    public static $ClassSpanTipo= [
        1 => 'span-blue',
        2 => 'span-red',
    ];

    public static $txTipo = [
        1 => 'Obra blanca',
        2 => 'Carpinteria',
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$ClassSpanTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$txTipo[$this->tipo]) . '</span>';
    }

}