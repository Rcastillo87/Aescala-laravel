<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

use App\Models\Proyecto;

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

}