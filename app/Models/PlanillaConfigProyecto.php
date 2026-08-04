<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaConfigProyecto extends Model
{
    use HasFactory;

    protected $table = 'planilla_confi_proyecto';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'valor_config',
        'tipo',
        'id_user',
        'id_proyecto'
    ];

    public static $ClassSpanTipo = [
        1 => 'span-blue',
        2 => 'span-red',
        3 => 'span-green',
    ];

    public static $txTipo = [
        1 => 'Valor área Proyecto',
        2 => 'Valor área Enchape',
        3 => 'Porcentajes del Proyecto',
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$ClassSpanTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$txTipo[$this->tipo]) . '</span>';
    }

    // 👇 ESTE es el que probablemente falta
    public function getValorAttribute()
    {
        if ((int) $this->tipo === 3) {
            $decoded = json_decode($this->valor_config, true);
            return is_array($decoded) ? $decoded : [];
        }
        return (float) $this->valor_config;
    }
}