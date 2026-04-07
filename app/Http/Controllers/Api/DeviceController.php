<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Dispositivo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $data = $request->validate([
            'device_serial' => [
                'required',
                'string',
                'min:10',
                'max:120',
                'regex:/^[A-Za-z0-9\-_\.]+$/'
            ],
            'manufacturer' => 'nullable|string|min:2|max:80',
            'model'        => 'nullable|string|min:2|max:80',
            'brand'        => 'nullable|string|min:2|max:80',
            'device'       => 'nullable|string|min:2|max:80',
            'nombre_equipo' => 'nullable|string|min:3|max:120',
            'imei_1'        => 'nullable|string|min:10|max:20',
            'imei_2'        => 'nullable|string|min:10|max:20',
            'id_user'       => 'nullable|exists:users,id',
            'app_version' => 'nullable|string|max:10',
        ]);

        $minVersion = env('APP_MIN_VERSION');
        $needsUpdate = false;

        if (!empty($data['app_version'])) {
            $needsUpdate = version_compare($data['app_version'], $minVersion, '<');
        }

        $idsAsignados = Dispositivo::whereNotNull('id_user')
            ->pluck('id_user')
            ->toArray();

        $users = User::query()
            ->whereIn('id_rol', [3,7,8])
            ->whereNotIn('id', $idsAsignados)
            ->get(['nombre_completo', 'id'])
            ->toArray();

        DB::beginTransaction();
        try {
            $device = Dispositivo::updateOrCreate(
                ['device_serial' => $data['device_serial']],
                $data
            );
            DB::commit();
            return $this->success([
                'device_id' => $device->id,
                'allUsers' => $users,
                'user' => $device->userAsignado?->only([
                    'id',
                    'email',
                    'nombre_completo',
                    'activo',
                    'id_rol',
                    'nameRol',
                    'estado'
                ])
            ], $needsUpdate ? 'Actualización recomendada' : 'Device registrado');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // Handler global captura
        }
    }

}
