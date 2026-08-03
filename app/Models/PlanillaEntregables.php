<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaEntregables extends Model
{
    use HasFactory;

    protected $table = 'planilla_entregables';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'tipo',
        'unidad',
        'cantidad',
        'valor_uni',
        'descripccion',
        'id_proyecto',
        'id_user',
    ];

    public static $ClassSpanTipo= [
        1 => 'span-blue',
        2 => 'span-red',
        3 => 'span-green',
        4 => 'span-orange'
    ];

    public static $txTipo = [
        1 => 'Adicionales contrato',
        2 => 'adicionales obra',
        3 => 'adicionales de carpintería',
        4 => 'Adicional mesónes'
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$ClassSpanTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$txTipo[$this->tipo]) . '</span>';
    }

}