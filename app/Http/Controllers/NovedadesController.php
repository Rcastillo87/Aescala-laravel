<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Novedades;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NovedadesController extends Controller
{
    public function index() 
    {
        Gate::authorize('novedades.index');

        $title = Auth::user()->isAdmin ? 'Novedades' : 'Mis Novedades';

        $perPage = request('per_page', 10);

        $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5, 2])
            ->when(!Auth::user()->isAdmin, function ($query) {
                $query->where(function ($q) {
                    $q->where('id_user', Auth::user()->id)
                    ->orWhere('id_user_obra_blanca', Auth::user()->id)
                    ->orWhere('id_user_diseno', Auth::user()->id);
                });
            })
            ->whereNotNull('cedula_cliente')
            ->whereNotNull('tipo_doc_cliente')
            ->orderBy('nombre_proyecto', 'ASC')
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        $items = Novedades::with('proyecto', 'user')
            ->when(!Auth::user()->isAdmin, function ($query) {
                $query->where('id_user', Auth::user()->id);
            })
            ->orderBy('createdAt', 'DESC')
            ->paginate($perPage)
            ->withQueryString();

        $estados = Novedades::$estado;

        return view('novedades.index', compact('title', 'proyectos', 'items', 'estados'));
    }

    public function delete($id)
    {
        Gate::authorize('novedades.delete');

        $novedad = Novedades::findOrFail($id);

        if ($novedad->estado !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se puede eliminar si está en estado Notificado.'
            ], 422);
        }

        $novedad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Novedad eliminada correctamente.'
        ]);
    }

    public function notificadoUpd($id)
    {
        Gate::authorize('novedades.notificadoUpd');

        $novedad = Novedades::findOrFail($id);

        if ($novedad->estado === 1) {
            $novedad->estado = 2; // Pasa a En Revisión
            $novedad->save();
        }

        return response()->json([
            'success' => true,
            'estado' => $novedad->estado,
            'spanEstado' => $novedad->span_estado
        ]);
    }

    public function saveNotificado(Request $request, $id)
    {
        Gate::authorize('novedades.saveNotificado');

        $novedad = Novedades::findOrFail($id);

        if (!in_array($novedad->estado, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'Esta acción solo se permite si está en estado Notificado o En Revisión.'
            ], 422);
        }

        $request->validate([
            'estado' => ['required', 'in:3,4'],
            'comentario' => ['required', 'string', 'max:1000']
        ]);

        $novedad->estado = $request->estado;
        $novedad->comentario = $request->comentario;
        $novedad->fecha_respuesta = now();
        $novedad->save();

        return response()->json([
            'success' => true,
            'message' => 'Comentario guardado y estado actualizado correctamente.'
        ]);
    }

    public function saveNovedad(Request $request)
    {
        Gate::authorize('novedades.saveNovedad');

        $request->validate([
            'id_proyecto' => ['required', 'exists:proyectos,id'],
            'novedades_array' => ['required', 'array', 'min:1'],
            'novedades_array.*' => ['required', 'string', 'max:1000']
        ]);

        // Concatenar las novedades del array usando '***'
        $novedadesConcatenadas = implode(' *** ', array_map('trim', $request->novedades_array));

        Novedades::create([
            'id_proyecto' => $request->id_proyecto,
            'id_user' => Auth::id(),
            'novedades' => $novedadesConcatenadas,
            'estado' => 1, // Estado inicial por defecto: Notificado
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Novedades registradas correctamente.'
        ]);
    }

}