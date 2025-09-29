<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zonas;
use App\Models\Otrosi;
use App\Models\User;

class OtrosiController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Otro Si';
        $items = Otrosi::with(['proyecto', 'user_encargado'])
        ->whereHas('proyecto', function ($query) {
            if (request('nombre_proyecto')) {
                $query->where('nombre_proyecto', 'like', '%' . request('nombre_proyecto') . '%');
            }
        })
        ->whereHas('user_encargado', function ($query) {
            if (request('id_userSerch')) {
                $query->where('id', request('id_userSerch'));
            }
        })
        ->paginate(10)
        ->appends(request()->query());

        $user = User::where('id_rol', 3)->get()->toArray();
        $headers = ['Nombre Proyecto', 'En Cargado', 'Cantidad', 'Valor', 'Opciones'];
        return view('otrosi.index', compact( 'title', 'items', 'headers', 'user'));
    }

    public function create( ) 
    {

    }
}
