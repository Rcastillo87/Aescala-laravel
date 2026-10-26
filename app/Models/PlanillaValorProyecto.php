<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaValorProyecto extends Model
{
    use HasFactory;

    protected $table = 'planilla_valores_proyecto';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'tipo',
        'valor',
        'id_user',
    ];

    protected $casts = [
        'id_proyecto' => 'integer',
        'tipo'        => 'integer',
        'valor'       => 'integer',
        'id_user'     => 'integer',
    ];

    /**
     * Tipos válidos en esta tabla.
     * Ojo: el tipo 3 (Porcentajes) ya NO vive aquí, vive en planilla_entregables.
     */
    public static $ClassSpanTipo = [
        1 => 'span-blue',
        2 => 'span-red',
        4 => 'span-orange',
        5 => 'span-gray',
        6 => 'span-blue',
    ];

    public static $txTipo = [
        1 => 'Valor área Proyecto',
        2 => 'Valor área Enchape',
        4 => 'Obra Blanca',
        5 => 'Carpintería',
        6 => 'Excedente Enchape',
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="' . (self::$ClassSpanTipo[$this->tipo] ?? 'default-class') . '">'
            . (self::$txTipo[$this->tipo] ?? 'Desconocido') . '</span>';
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}