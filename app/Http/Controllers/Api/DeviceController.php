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
        ]);
    }
}
