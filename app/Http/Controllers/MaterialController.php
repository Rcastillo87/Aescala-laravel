<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

use App\Models\InventarioMaterial;


class MaterialController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Materiales';
        $estado = InventarioMaterial::$estado;
        $tipos = InventarioMaterial::$tipo;
        $items = InventarioMaterial::when(Request('nombre_material'), function ($query, $nombre_material) { 
            return $query->whereRaw('LOWER(nombre_material) LIKE LOWER(?)', ["%$nombre_material%"]);
        })
        ->when(is_numeric(Request('cantidad')), function ($query) { 
            return $query->where('cantidad', Request('cantidad'));
        })
        ->when(is_numeric(Request('cantidad_min')), function ($query) { 
            return $query->where('cantidad_min', Request('cantidad_min'));
        })
        ->when(is_numeric(Request('valor_unidad')), function ($query) { 
            return $query->where('valor_unidad', Request('valor_unidad'));
        })
        ->when(Request('tipo'), function ($query, $tipo) { 
            return $query->where('tipo', $tipo);
        })
        ->when(Request('activo'), function ($query, $activo) { 
            return $query->where('activo', $activo);
        })
        ->paginate(10);
        $headers = ['Nombre Material', 'Stock', 'Stock Min', 'Valor', 'Tipo Material', 'Estado', 'Opciones'];
        return view('material.index', compact( 'title', 'items', 'headers', 'estado','tipos'));
    }

    public function create( ) 
    {
        $material = null;
        $title = 'Crear Materiales';
        $action = 'Crear';
        $unidades = InventarioMaterial::$unidades;
        $tipos = InventarioMaterial::$tipo;
        return view('material.create', compact( 'title', 'action', 'unidades', 'tipos', 'material'));
    }

    public function edit($id) 
    {
        $material = InventarioMaterial::find($id);
        $title = 'Editar Materiales';
        $action = 'Crear';
        $unidades = InventarioMaterial::$unidades;
        $tipos = InventarioMaterial::$tipo;
        return view('material.create', compact( 'title', 'action', 'unidades', 'tipos', 'material'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'cantidad' => 'required|numeric|min:0',
            'cantidad_min' => 'required|numeric|min:0',
            'id_unidad' => Rule::in(array_keys(InventarioMaterial::$unidades)),
            'valor_unidad' => 'required|numeric|min:0',
            'tipo' => Rule::in(array_keys(InventarioMaterial::$tipo)),
            'descripccion' => 'nullable|string',
            'nombre_material' => 'required|string'
        ]);
        
        if ( Auth::user()->id_rol == 2 ) {
            $data = array_diff_key($data, ['cantidad' => '']);
        }

        if (!$req->id) {
            $msg = 'Material creado con éxito';
        } else {
            $msg = 'Material editado con éxito';
        }

        try {
            DB::beginTransaction();
            InventarioMaterial::updateOrCreate(
                ['id' => $req->id],
                $data
            );
            DB::commit();
            return redirect()->route('material.index')->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function editStatus($id) 
    {
        try {
            DB::beginTransaction();
            $user = InventarioMaterial::findOrFail($id);
            $user->update(['activo' => ($user->activo == 1) ? 2 : 1]);
            DB::commit();
            return response()->json([
                'status' => true, 
                'message' => 'Material actualizado correctamente.'
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el aterial: ' . $e->getMessage()
            ], 500);
        }
    }
}