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
        'id_proyecto',
        'tipo',
        'items',
        'id_user',
    ];

    protected $casts = [
        'id_proyecto' => 'integer',
        'tipo'        => 'integer',
        'items'       => 'array',   // JSON ↔ array automático
        'id_user'     => 'integer',
    ];

    public static $ClassSpanTipo = [
        1 => 'span-blue',
        2 => 'span-red',
        3 => 'span-green',
        4 => 'span-orange',
        5 => 'span-purple',   // Porcentajes del Proyecto
    ];

    public static $txTipo = [
        1 => 'Adicionales contrato',
        2 => 'Adicionales obra',
        3 => 'Adicionales de carpintería',
        4 => 'Adicional mesones',
        5 => 'Porcentajes del Proyecto',
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

    /* ==========================================================
     *  HELPERS DE COBROS (tipo 5 — Porcentajes del Proyecto)
     *  Antes vivían en PlanillaConfigProyecto; ahora operan sobre
     *  los ítems del JSON en esta tabla.
     * ========================================================== */

    /**
     * Valor máximo cobrable de un ítem del tipo 5, redondeado al peso.
     * Un ítem sin 'en_pesos' se trata como porcentaje.
     */
    public static function montoItem(array $item, float $valorBase): int
    {
        $valor = (float) ($item['porcentage'] ?? 0);

        if ((int) ($item['en_pesos'] ?? 0) === 1) {
            return (int) round($valor);
        }

        return (int) round($valorBase * ($valor / 100));
    }

    /**
     * Devuelve los campos de cobro de un ítem con valores por defecto.
     * Sin cobro: valor null, aprobacion 0, fecha null.
     */
    public static function normalizarCobro(array $item): array
    {
        $valor = (float) ($item['valor_cobrar'] ?? 0);
        $aprob = (int) ($item['aprobacion'] ?? 0);

        return [
            'valor_cobrar'     => $valor > 0 ? (int) round($valor) : null,
            'valor_cobrar_por' => $item['valor_cobrar_por'] ?? null,
            'aprobacion'       => in_array($aprob, [50, 100], true) ? $aprob : 0,
            'aprobacion_por'   => $item['aprobacion_por'] ?? null,
            'aprobacion_at'    => $item['aprobacion_at'] ?? null,
            'fecha_pago'       => !empty($item['fecha_pago']) ? $item['fecha_pago'] : null,
            'fecha_pago_por'   => $item['fecha_pago_por'] ?? null,
        ];
    }

    /**
     * Valor que se paga según la aprobación (50% o 100% del valor a cobrar).
     */
    public static function valorAprobado(?int $valorCobrar, int $aprobacion): int
    {
        if (!$valorCobrar || $aprobacion <= 0) {
            return 0;
        }

        return (int) round($valorCobrar * $aprobacion / 100);
    }
}