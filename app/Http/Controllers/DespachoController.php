<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

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
        ->get(['id','nombre_material', 'cantidad', 'valor_unidad', 'id_unidad', 'tipo'])->toArray();

        return view('despachos.index', compact('title', 'tipo', 'colaUsers', 'proyectos', 'materiales'));
    }

    public function save( Request $request ) 
    {
        dd( $request->all() );
    }
}