<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'cantidad',
        'id_material',
        'id_solicitud'
    ];

    // Relación con el modelo InventarioMaterial
    public function material()
    {
        return $this->belongsTo(InventarioMaterial::class, 'id_material');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudMaterial::class, 'id_solicitud');
    }

    public function dataCotizacion($id)
    {
        $rawDate = config('database.default') === 'sqlite'
        ? "strftime('%Y-%m-%d', fecha_solicitud)"
        : "DATE_FORMAT(fecha_solicitud, '%Y-%m-%d')";

        $item = SolicitudMaterial::with('usuario')
            ->select(DB::raw("{$rawDate} as formattedDate"), 'id_user', 'id')
            ->find($id);

        $lista = [[
            'codigo' => 'xxxxxxxx',
            'createdAt' => $item->formattedDate,
            'spanEstado' => '<span class="span-green">Cotizacion</span>',
            'nombre_completo' => $item->usuario->nombre_completo ?? null,
            'user_despacha' => 'N/A'
        ]];

        $items = self::with(['material' => function($query) {
                            $query->select('id', 'nombre_material', 'tipo', 'valor_unidad');
                        }])
                        ->where('id_solicitud', $id)
                        ->select('id', 'id_material', 'cantidad')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'codigo' => 'xxxxxxxx',
                                'id_material' => $item->id_material,
                                'cantidad' => $item->cantidad,
                                'valor_unidad' => $item->material->valor_unidad,
                                'isCobro' => '--',
                                'nombre_material' => $item->material->nombre_material ?? null,
                                'spanTipo' => $item->material->spanTipo ?? null,
                                'ref_devolucion' => '--'
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
