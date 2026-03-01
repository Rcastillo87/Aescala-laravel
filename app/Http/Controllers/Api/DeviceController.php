<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
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

        $device = Dispositivo::firstOrCreate(
            ['device_serial'=>$data['device_serial']],
            $data
        );

        return response()->json([
            'device_id' => $device->id,
            'user' => $device->userAsignado()
        ]);
    }
}
    /** URL base de tu API Laravel (sin slash final) */
    //const val BASE_URL = "https://srv685835.hstgr.cloud/api"

    /** Clave secreta HMAC — debe coincidir con API_SECRET_KEY en tu .env */
    //const val HMAC_SECRET = "5939874c3fa1e3e671e2b88ff1b26711efe26f532b025259d650587e8be103ac"
