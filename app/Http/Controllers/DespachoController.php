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
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        $materiales = InventarioMaterial::where('activo', 1) 
        ->get(['id','nombre_material', 'cantidad', 'valor_unidad', 'spanTipo', 'unidades', 'id_unidad', 'tipo', 'descripccion'])
        ->toArray();

        return view('despachos.index', compact('title', 'tipo', 'colaUsers', 'proyectos', 'materiales'));
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
                'numeric',
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
            'materiales.*.valor_unidad' => ['required', 'integer']
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
                Despachos::create($dato);

                $inventarioMaterial = InventarioMaterial::find($material['id_material']);
    
                // **Actualizar inventario**
                $msg = '';
                if ($validated['tipo'] == 2) {
                    // Si el tipo es 2, se suma la cantidad
                    $msg = 'Devolucion';
                    $inventarioMaterial->increment('cantidad', $material['cantidad']);
                } else {
                    // Si no, se descuenta
                    $msg = 'Despacho';
                    $inventarioMaterial->decrement('cantidad', $material['cantidad']);
                }
            }
    
            return redirect()->route('despachos.index')
                        ->with('success', "$msg {$codigo} registrado correctamente");
        });
    }
    
}