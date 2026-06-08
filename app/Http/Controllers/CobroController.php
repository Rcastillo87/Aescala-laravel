<?php

namespace App\Http\Controllers;

use App\Models\CobroRefe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CobroController extends Controller
{
    public function index( )
    {
        Gate::authorize('cobro.index');
        $title = 'Lista de Cobros';
        $items = CobroRefe::with('proyecto')
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends(request()->query());

        return view('cobro.index', compact('title', 'items'));
    }

    public function deleteCobro($id)
    {
        Gate::authorize('cobro.deleteCobro');
        $cobro = CobroRefe::findOrFail($id);
        DB::beginTransaction();
        try {
            $cobro->delete();
            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Cobro eliminado correctamente.',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al eliminar el cobro.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function sendAcuerdoPago(Request $request)
    {
        Gate::authorize('cobro.sendAcuerdoPago');

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:cobro_refe,id',
            'fecha_acuerdo_pago' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        try {
            $cobro = CobroRefe::findOrFail($request->id);
            $cobro->fecha_acuerdo_pago = $request->fecha_acuerdo_pago;
            $cobro->save();
            DB::commit();
            return response()->json([
                'success'  => true,
                'message' => 'Acuerdo de pago actualizado correctamente.',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Error al actualizar el acuerdo de pago.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function sendNotificacion(Request $request)
    {
        Gate::authorize('cobro.sendNotificacion');

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:cobro_refe,id',
            'fecha_notificacion' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        try {
            $cobro = CobroRefe::findOrFail($request->id);
            $cobro->fecha_notificacion = $request->fecha_notificacion;
            $cobro->save();
            DB::commit();
            return response()->json([
                'success'  => true,
                'message' => 'Notificación actualizada correctamente.',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success'  => false,
                'message' => 'Error al actualizar la notificación.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}