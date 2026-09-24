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
        4 => 'span-orange',
        5 => 'span-gray',
        6 => 'span-blue'
    ];

    public static $txTipo = [
        1 => 'Valor área Proyecto',
        2 => 'Valor área Enchape',
        3 => 'Porcentajes del Proyecto',
        4 => 'Obra Blanca',
        5 => 'Carpinteria',
        6 => 'Excedente Enchape'
    ];

    public function getSpanTipoAttribute()
    {
        return '<span class="'.(self::$ClassSpanTipo[$this->tipo] ?? 'default-class').'">'
             . (self::$txTipo[$this->tipo]) . '</span>';
    }

    public function getValorAttribute()
    {
        if ((int) $this->tipo === 3) {
            $decoded = json_decode($this->valor_config, true);
            return is_array($decoded) ? $decoded : [];
        }
        return (float) $this->valor_config;
    }

    /* ==========================================================
     *  COBROS POR ÍTEM (Tipo 3)
     *  Los datos de cobro viven dentro del mismo JSON de cada ítem:
     *  valor_cobrar, aprobacion (0|50|100), fecha_pago (Y-m-d) y
     *  quién guardó cada paso.
     *  La clave de cada ítem es su posición en el arreglo: la lista
     *  no cambia después de aceptar la configuración (solo se elimina
     *  completa y se vuelve a aceptar).
     * ========================================================== */

    /**
     * Valor máximo cobrable de un ítem, redondeado al peso
     * (igual al monto que se muestra en pantalla).
     * Un ítem sin la clave 'en_pesos' se trata como porcentaje.
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