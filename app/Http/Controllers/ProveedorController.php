<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Proveedores';
        $items = Proveedor::
        when(Request('razon_social'), function ($query, $razon_social) { 
            return $query->whereRaw('LOWER(razon_social) LIKE LOWER(?)', ["%$razon_social%"]);
        })
        ->when(Request('nit'), function ($query, $nit) { 
            return $query->whereRaw('LOWER(nit) LIKE LOWER(?)', ["%$nit%"]);
        })
        ->when(Request('direccion'), function ($query, $direccion) { 
            return $query->whereRaw('LOWER(direccion) LIKE LOWER(?)', ["%$direccion%"]);
        })
        ->when(Request('telefono'), function ($query, $telefono) { 
            return $query->whereRaw('LOWER(telefono) LIKE LOWER(?)', ["%$telefono%"]);
        })
        ->when(Request('activo'), function ($query, $activo) { 
            return $query->where('activo', $activo);
        })
        ->paginate(10)
        ->appends(request()->query());
    
        $activo = Proveedor::$estado;
        $headers = ['Nombre | Razón', 'Documento | NIT', 'Direccion', 'Telefono', 'Fecha de Creacion', 'Estado', 'Opciones'];
        return view('proveedor.index', compact('title', 'items', 'headers', 'activo'));
    }

    public function create( ) 
    {
        return $this->form();
    }

    public function edit($id) 
    {
        return $this->form($id);
    }

    public function form($id = null)
    {
        $proveedor = $id?Proveedor::find($id):null;
        $title = $id?'Editar Proveedor':'Crear Proveedor';
        $estado = Proveedor::$estado;
        return view('proveedor.create', compact('title', 'proveedor', 'estado'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'razon_social' => 'required|string|max:100',
            'nit' => 'required|string|max:14',
            'direccion' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20'
        ]);

        $msg = ucfirst($req->id ? 'Proveedor editado con éxito' : 'Proveedor creado con éxito');
    
        try {
            DB::beginTransaction();
            Proveedor::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
            return redirect()->route('proveedor.index')->with('success', $msg);
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
            $user = Proveedor::findOrFail($id);
            $user->update(['activo' => ($user->activo == 1) ? 2 : 1]);
            DB::commit();
            return response()->json([
                'status' => true, 
                'message' => 'Proveedor actualizado correctamente.'
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
}