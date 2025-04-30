<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Despachos extends Model
{
    use HasFactory;

    protected $table = 'inventario_solicituds';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'tipo',
        'codigo',
        'id_material',
        'id_user',
        'id_proyecto',
        'cantidad',
        'valor_unidad'
    ];

    protected $appends = ['spanEstado'];

    protected $casts = [
        'tipo' => 'integer',
    ];

    public static function generarCodigoUnico()
    {
        do {
            $codigo = strtoupper(Str::random(10)); // GUID de 10 caracteres
        } while (self::where('codigo', $codigo)->exists());
        
        return $codigo;
    }

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$classTipo[(int)$this->tipo] ?? 'default-class').'">'
        . (self::$tipo[(int)$this->tipo] ?? 'Desconocido') . '</span>';
    }

    public static $tipo = [
        1 => 'Despachado',
        2 => 'Devolucion'
    ];

    public static $classTipo = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    // Relación con el modelo InventarioMaterial
    public function material()
    {
        return $this->belongsTo(InventarioMaterial::class, 'id_material');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function despachos(int $id, $codigo = null)
    {

        $rawDate = config('database.default') === 'sqlite' 
        ? "strftime('%Y-%m-%d', createdAt)" 
        : "DATE_FORMAT(createdAt, '%Y-%m-%d')";

        $lista =  Despachos::with('user')
                        ->where('id_proyecto', $id)
                        ->when($codigo, function ($query, $codigo) {
                            return $query->where('codigo', $codigo);
                        })
                        ->select('tipo', 'codigo', DB::raw("{$rawDate} as formattedDate"), 'id_user')
                        ->distinct()
                        ->get()
                        ->map(function ($item) {
                            return [
                                'codigo' => $item->codigo,
                                'createdAt' => $item->formattedDate,
                                'spanEstado' => $item->spanEstado,
                                'nombre_completo' => $item->user->nombre_completo
                            ];
                        })
                        ->toArray();

        $items = Despachos::with(['material' => function($query) {
                            $query->select('id', 'nombre_material', 'tipo');
                        }])
                        ->where('id_proyecto', $id)
                        ->when($codigo, function ($query, $codigo) {
                            return $query->where('codigo', $codigo);
                        })
                        ->select('codigo', 'id_material', 'cantidad', 'valor_unidad')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'codigo' => $item->codigo,
                                'id_material' => $item->id_material,
                                'cantidad' => $item->cantidad,
                                'valor_unidad' => $item->valor_unidad,
                                'nombre_material' => $item->material->nombre_material ?? null,
                                'spanTipo' => $item->material->spanTipo ?? null
                            ];
                        })
                        ->toArray();

        $data = [];
        foreach ($lista as $val1) {
            $despacho = [];
            foreach ($items as $val2) {
                if($val1['codigo'] == $val2['codigo']){
                    $despacho[] = $val2;
                }
            }
            $val1['items'] = $despacho;
            $data[] = $val1;
        }
        return $data;
    }

}