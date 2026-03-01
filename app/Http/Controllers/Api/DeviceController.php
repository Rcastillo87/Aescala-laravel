<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Dispositivo;
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
        ]);

        DB::beginTransaction();
        try {
            $device = Dispositivo::firstOrCreate(
                ['device_serial' => $data['device_serial']],
                $data
            );
            DB::commit();
            return $this->success([
                'device_id' => $device->id,
                'user' => $device->userAsignado?->only([
                    'id',
                    'email',
                    'nombre_completo',
                    'activo',
                    'id_rol',
                    'nameRol',
                    'estado'
                ])
            ], 'Device registrado');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // Handler global captura
        }
    }

}