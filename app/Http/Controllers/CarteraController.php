<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Area;

class CarteraController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Cartera';
        $usuario = Auth::user();

        $items_1 = Proyecto::with(['user'])
            ->whereIn('id_estado', [1, 3, 5])
            ->WhereRaw('(termino_1_por + termino_2_por + termino_3_por + termino_4_por + termino_5_por + termino_6_por) > 0')
            //->where('a_paz', 0)
            ->paginate(10)
            ->appends(request()->query());

        $items_2 = Otrosi::with(['user_encargado'])
            ->where('estado', 1)
            //->where('a_paz', 0)
            ->whereHas('proyecto', function ($query) {
                $query->whereIn('id_estado', [1, 3, 5]);
            })
            ->paginate(10)
            ->appends(request()->query());

        $headers_1 = ['Proyecto', 'Cliente', 'En Cargado',  'Estado Proyecto', 'Pagos / Total', 'Opciones'];

        $headers_2 = ['Proyecto', 'Numero Otro Si',  'En Cargado', 'Estado Otro Si', 'Opciones'];

        return view('cartera.index', compact( 'title', 'items_1', 'items_2', 'headers_1', 'headers_2'));
    }

    public function pagos($id)
    {
        $item = Proyecto::findOrFail($id);
        $pagos = $item->pagos;
        return response()->json([
            'status' => true,
            'message' => 'Consulta exitosa',
            'data' => $pagos,
        ], 200);
    }





}
