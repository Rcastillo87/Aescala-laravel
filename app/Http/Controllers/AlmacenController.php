<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacenes;
use App\Models\User;
use App\Models\InventarioMaterial;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AlmacenController extends Controller
{
    public function index( )
    {
        Gate::authorize('almacen.index');

        $title = 'Lista de Almacenes';
        $items = Almacenes::with('user')->get();
        $users = User::where('id_rol', 9)->get()->toArray();
        return view('almacen.index', compact('title', 'items', 'users'));
    }

    public function create( )
    {
        Gate::authorize('almacen.create');
        return $this->form();
    }

    public function edit($id)
    {
        Gate::authorize('almacen.edit');
        return $this->form($id);
    }

    public function form($id = null)
    {
        $title = $id?'Editar Almacen':'Crear Almacen';
        $data = $id?Almacenes::find($id):null;
        $tipos = Almacenes::$tipo;
        $users = User::where('id_rol', 9)->get()->toArray();
        return view('almacen.create', compact('title', 'data', 'tipos', 'users'));
    }

    public function save(Request $req){
        Gate::authorize('almacen.save');
        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_almacen' => 'required|string|max:100',
            'id_user' => [
                'required',
                'integer',
                Rule::unique('almacenes', 'id_user')->ignore($req->id)
            ],

            'editar' => ['required', 'integer', Rule::in(array_keys(Almacenes::$txEditar))],
            'tipo' => ['required', 'integer', Rule::in(array_keys(Almacenes::$tipo))],
        ]);

        $msg = ucfirst($req->id ? 'Almacen editado con éxito' : 'Almacen creado con éxito');

        try {
            DB::beginTransaction();
            Almacenes::updateOrCreate(['id' => $data['id'] ?? null], $data);
            DB::commit();

            return redirect()->route('almacen.index')->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

}
