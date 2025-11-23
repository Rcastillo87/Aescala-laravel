<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\SolicitudMaterial;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\SolicitudItems;


class SolicitudController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Solicitud de Material';
        $items = SolicitudMaterial::with(['proyecto', 'usuario'])
        ->paginate(10)
        ->appends(request()->query());
        
        $headers = ['Nombre del Proyecto', 'Quien Solicito', 'Fecha de solicitud', 'Items Entregados', 'Estado', 'Opciones'];
        return view('solicitud.index', compact('title', 'items', 'headers'));
    }

    public function create( )
    {
        $title = 'Crear Solicitud de Material';
        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->get(['id', 'id_user', 'nombre_proyecto'])
            ->toArray();
	    $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();
        return view('solicitud.create', compact('title', 'materiales', 'proyectos'));
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
                'integer',
                'min:1'
            ],
            'observacion' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {

            $solicitud = SolicitudMaterial::create([
                'id_user'         => $validated['id_user'],
                'id_proyecto'     => $validated['id_proyecto'],
                'fecha_solicitud' => now(),
                'estado'          => 1,
                'observacion'     => $validated['observacion'] ?? null,
            ]);

            foreach ($validated['materiales'] as $material) {
                SolicitudItems::create([
                    'id_solicitud' => $solicitud->id,
                    'id_material'  => $material['id_material'],
                    'cantidad'     => $material['cantidad'],
                    'estado'       => 1
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Solicitud registrada correctamente',
            ], 200);
        });
    }

}