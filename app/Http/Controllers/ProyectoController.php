<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proyecto;
use App\Models\User;

class ProyectoController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Proyectos';
        $estado = Proyecto::$estado;
        $items = Proyecto::when(Request('nombre_completo'), function ($query, $nombre_completo) { 
            return $query->whereRaw('LOWER(nombre_completo) LIKE LOWER(?)', ["%$nombre_completo%"]);
        })
        ->paginate(10);
        return view('proyecto.index', compact('title', 'items', 'estado'));
    }

    public function create( ) 
    {
        $proyecto = null;
        $colaUsers = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->map(fn($user) => ['id' => $user->id, 'nombre_completo' => $user->nombre_completo])
        ->toArray();
        $title = 'Crear Proyecto';
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));
        return view('proyecto.create', compact('title', 'proyecto', 'colaUsers', 'departamentos'));
    }

}