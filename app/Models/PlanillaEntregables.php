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
        'porcentage'
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

    // Devuelve el valor ya "listo para usar": número para tipo 1/2, array para tipo 3
    public function getValorAttribute()
    {
        if ((int) $this->tipo === 3) {
            return json_decode($this->valor_config, true) ?? [];
        }
        return (float) $this->valor_config;
    }

}