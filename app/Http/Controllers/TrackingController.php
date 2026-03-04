<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\Georreferencia;
use App\Models\user;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        $title = 'Dispositivos En Mapa';

        $users = User::query()
            ->whereIn('id_rol', [3,7,8])
            ->whereNotIn('id', function ($q) {
                $q->select('id_user')
                ->from('dispositivo');
            })
            ->pluck('nombre_completo', 'id')
            ->toArray();

        $dispositivos = Dispositivo::with(['ultimaUbicacion', 'userAsignado'])->get()
            ->map(function ($d) {
                return [
                    'id'            => $d->id,
                    'device_serial' => $d->device_serial,
                    'brand'         => $d->brand,
                    'model'         => $d->model,
                    'last_location' => $d->ultimaUbicacion,
                    'user'          => $d->userAsignado,  // ← agregar esto
                ];
            });

        return view('tracking.index', compact('dispositivos', 'title', 'users'));
    }

    public function asination(Request $request)
    {
        $request->validate([
            'device_id' => 'required|integer|exists:dispositivo,id',
            'user_id'   => 'nullable|integer|exists:users,id',
        ]);

        $device = Dispositivo::find($request->device_id);
        $device->id_user = $request->user_id;
        $device->save();

        return response()->json(['status' => true, 'message' => 'Dispositivo asignado correctamente']);
    }

    /**
     * Tiempo real: última ubicación de todos (o uno) – polling cada N segundos
     */
    public function realtime(Request $request)
    {
        $ids = $request->input('ids'); // null = todos

        $query = Dispositivo::with(['ultimaUbicacion']);

        if ($ids) {
            $query->whereIn('id', (array) $ids);
        }

        $data = $query->get()->map(function ($d) {
            $loc = $d->ultimaUbicacion;
            if (!$loc) return null;

            return [
                'device_id'   => $d->id,
                'label'       => "{$d->brand} {$d->model} ({$d->device_serial})",
                'lat'         => (float) $loc->lat,
                'lng'         => (float) $loc->lng,
                'accuracy'    => $loc->accuracy,
                'battery'     => $loc->battery,
                'tipo'        => $loc->tipo,
                'tipo_label'  => Georreferencia::$tipos[$loc->tipo] ?? 'Ubicación',
                'signal_level'=> $loc->signal_level,
                'signal_label'=> Georreferencia::$signalLevels[$loc->signal_level] ?? '–',
                'network_type'=> $loc->network_type,
                'net_label'   => Georreferencia::$networkTypes[$loc->network_type] ?? '–',
                'updated_at'  => $loc->request_at,
            ];
        })->filter()->values();

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Recorrido histórico de un dispositivo en un rango de fechas
     */
    public function history(Request $request)
    {
        $request->validate([
            'device_id' => 'required|integer|exists:dispositivo,id',
            'date'      => 'required|date',
        ]);

        $puntos = Georreferencia::where('device_id', $request->device_id)
            ->orderBy('request_at')
            ->get(['id', 'lat', 'lng', 'battery', 'tipo', 'signal_level', 'network_type', 'request_at', 'accuracy']);

        $device = Dispositivo::find($request->device_id);

        return response()->json([
            'status' => true,
            'data'   => [
                'device' => "{$device->brand} {$device->model}",
                'date'   => $request->date,
                'total'  => $puntos->count(),
                'points' => $puntos->map(fn($p) => [
                    'lat'          => (float) $p->lat,
                    'lng'          => (float) $p->lng,
                    'battery'      => $p->battery,
                    'tipo'         => $p->tipo,
                    'tipo_label'   => Georreferencia::$tipos[$p->tipo] ?? 'Ubicación',
                    'signal_level' => $p->signal_level,
                    'at'           => $p->request_at,
                ]),
            ]
        ]);
    }
}
