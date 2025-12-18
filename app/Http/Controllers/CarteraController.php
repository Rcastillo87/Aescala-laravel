<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\Proyecto;
use App\Models\Pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            ->paginate(10, ['*'], 'page_proyectos') 
            ->appends(request()->query());
            
        $items_2 = Otrosi::with(['user_encargado'])
            ->where('estado', 1)
            //->where('a_paz', 0)
            ->whereHas('proyecto', function ($query) {
                $query->whereIn('id_estado', [1, 3, 5]);
            })
            ->paginate(10, ['*'], 'page_otrosi')
            ->appends(request()->query());

        $headers_1 = ['Proyecto', 'Cliente', 'En Cargado',  'Estado Proyecto', 'Pagos / Total', 'Paz & Salvo', 'Opciones'];

        $headers_2 = ['Otro Si',  'Cliente', 'En Cargado', 'Estado Otro Si', 'Pagos / Total', 'Paz & Salvo', 'Opciones'];

        return view('cartera.index', compact( 'title', 'items_1', 'items_2', 'headers_1', 'headers_2'));
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proyecto_id'  => 'required|integer',
            'tipo'         => 'required|integer|in:1,2',
            'valor_pagado' => 'required|numeric|min:0',
            'comentario'   => 'nullable|string|max:500',
            'concepto'     => 'required|integer|between:1,6',
            'fv'           => 'required|string|max:20',
            'fecha_pago'   => 'required|date',
        ], [
            // Mensajes personalizados (opcional pero recomendado)
            'required' => 'Este campo es obligatorio.',
            'integer'  => 'Debe ser un número válido.',
            'numeric'  => 'Debe ser un valor numérico.',
            'between'  => 'Valor fuera del rango permitido.',
            'in'       => 'Valor inválido.',
            'min'      => 'El valor debe ser mayor a 0.',
            'date'     => 'Fecha inválida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Hay errores en el formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $pago = Pagos::create([
                'id_proyecto'  => $request->proyecto_id,
                'tipo_pago'    => $request->tipo,
                'valor_pagado' => $request->valor_pagado,
                'fecha_pago'   => $request->fecha_pago,
                'comentario'   => $request->comentario,
                'concepto'     => $request->concepto,
                'fv'           => $request->fv,
            ]);

            // Si el pago deja en balance
            if ($pago->valance) {
                $proyecto = Proyecto::find($request->proyecto_id);
                $proyecto->paz_salvo = 1;
                $proyecto->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Pago registrado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al registrar el pago.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }  

    public function pagos($id)
    {
        $item = Proyecto::findOrFail($id);
        $pagos = $item->pagos;
        $lista = $item->pagos()->where('tipo_pago', 1)->get();
        $totalApagar = $item->total ?? 0; 
        return response()->json([
            'status' => true,
            'message' => 'Consulta exitosa',
            'data' => [
                'id_proyecto' => $item->id,
                'pagado' => $lista,
                'conceptos_pago' => $pagos,
                'total_apagar' => $totalApagar,
            ],
        ], 200);
    }

    public function pagosOtroSi($id)
    {
        $item = Otrosi::with('pagos')->findOrFail($id);
        $deve = ($item->totalDeve ?? 0) - ($item->totalPago ?? 0);
        $pagos = $item->pagos()->where('tipo', 2)->get();
        return response()->json([
            'status' => true,
            'message' => 'Consulta exitosa',
            'data' => [
                'id_proyecto' => $item->id_proyecto,
                'pagados' => $pagos,
                'deve' => $deve,
            ],
        ], 200);
    }

}
