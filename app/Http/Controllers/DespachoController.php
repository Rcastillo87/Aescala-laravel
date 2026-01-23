<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Despachos;
use App\Models\Proyecto;
use App\Models\InventarioMaterial;

class DespachoController extends Controller
{

    public function index( ) 
    {
        $title = 'Despacho de Material';
        $tipo = Despachos::$tipo;
        $colaUsers = User::whereIn('id_rol', [3, 7, 8]) // Tecnico y Contratista y architecto
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->get(['id', 'id_user', 'nombre_proyecto'])
            ->toArray();

        return view('despachos.index', compact('title', 'tipo', 'colaUsers', 'proyectos'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'tipo' => ['required', 'integer', Rule::in(array_keys(Despachos::$tipo))],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.id_material' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail('El material seleccionado no está disponible.');
                    }
                }
            ],
            'materiales.*.cantidad' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $materialId = $request->input("materiales.{$index}.id_material");
                    $material = InventarioMaterial::find($materialId);
    
                    if ($material && $request->tipo != 2 && $value > $material->cantidad) {
                        $fail("La cantidad para {$material->nombre_material} excede el stock ({$material->cantidad}).");
                    }
                }
            ],
            'materiales.*.valor_unidad' => ['required', 'integer'],
            'materiales.*.valor_inventario' => ['required', 'integer'],
            'materiales.*.cobro' => ['required', 'integer', 'in:0,1']
        ]);

        // Iniciar transacción
        return DB::transaction(function () use ($validated) {
            
            $codigo = Despachos::generarCodigoUnico();//codigo de este despacho unico para este depacho
            $dato = [
                'tipo' => $validated['tipo'],
                'codigo' => $codigo,
                'id_user' => $validated['id_user'],
                'id_proyecto' => $validated['id_proyecto'],
            ];

            // Procesar materiales
            foreach ($validated['materiales'] as $material) {

                // Crear el despacho
                $dato['id_material'] = $material['id_material'];
                $dato['cantidad'] = $material['cantidad'];
                $dato['valor_unidad'] = $material['valor_unidad'];
                $dato['valor_inventario'] = $material['valor_inventario'];
                $dato['cobro'] = $material['cobro'];
                Despachos::create($dato);

                $inventarioMaterial = InventarioMaterial::find($material['id_material']);
    
                // **Actualizar inventario**
                $msg = '';
                if ($validated['tipo'] == 2) {
                    // Si el tipo es 2, se suma la cantidad
                    $msg = 'Devolucion';
                    //if($material['cobro'] == 1){
                        $inventarioMaterial->increment('cantidad', $material['cantidad']);
                    //}
                } else {
                    // Si no, se descuenta
                    $msg = 'Despacho';
                    //if($material['cobro'] == 1){
                        $inventarioMaterial->decrement('cantidad', $material['cantidad']);
                    //}
                }
            }

            $pdfRoute = route('proyecto.pdfDespacho', [
                'id' => $validated['id_proyecto'],
                'codigo' => $codigo,
                'view' => 1
            ]);

            return redirect()->route('despachos.index')
                ->with('success', "$msg {$codigo} registrado correctamente")
                ->with('pdf_url', $pdfRoute);
        });
    }

    public function selectMaterales(Request $req)
    {
        if($req->input('tipo') == 1) {
            $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();
        } else {
            $materiales = Despachos::query()
                ->where('tipo', 1)
                ->whereHas('material', function ($query) {
                    $query->where('activo', 1);
                })
                ->where('id_proyecto', $req->input('id_proyecto'))
                ->select(
                    'id_material',
                    DB::raw('SUM(cantidad) as total_cantidad'),
                    DB::raw('(
                        SELECT valor_unidad
                        FROM inventario_solicituds d2
                        WHERE d2.id_material = inventario_solicituds.id_material
                        AND d2.tipo = 1
                        ORDER BY d2.createdAt DESC
                        LIMIT 1
                    ) as valor_unidad')
                )
                ->groupBy('id_material')
                ->with('material')
                ->get()
                ->map(fn ($item) => [
                    'id'                => $item->material->id,
                    'nombre_material' => $item->material->nombre_material,
                    'codigo'         => $item->material->codigo,
                    'cantidad'        => $item->total_cantidad,
                    'cantidad_min'    => $item->material->cantidad_min,
                    'valor_unidad'    => $item->valor_unidad,
                    'valor_inventario'=> $item->cantidad * $item->valor_unidad,
                    'descripccion'    => $item->material->descripccion,
                    'id_unidad'      => $item->material->id_unidad,
                    'createdAt'      => $item->material->createdAt,
                    'updatedAt'      => $item->material->updatedAt,
                    'tipo'           => $item->material->tipo,
                    'activo'         => $item->material->activo,
                    'aprobar'        => $item->material->aprobar,
                    'id_proveedor'   => $item->material->id_proveedor,
                    'spanTipo'        => $item->material->spanTipo,
                    'unidades'        => $item->material->unidades,
                ]);
        }

        return response()->json(['status' => 'true', 'results' => $materiales], 200);
    }
    
}