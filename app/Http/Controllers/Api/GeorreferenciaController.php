<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Georreferencia;
use Illuminate\Http\Request;

class GeorreferenciaController extends Controller
{
    public function report(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:1000',
            'battery'  => 'nullable|integer|min:0|max:100',
            'request_at' => 'required|date|before_or_equal:now|after:2020-01-01',
        ]);

        $device = $request->device; // lo inyecta el middleware

        Georreferencia::create([
            'dispositivo_id' => $device->id,
            'lat'            => $data['lat'],
            'lng'            => $data['lng'],
            'accuracy'       => $data['accuracy'] ?? null,
            'battery'        => $data['battery'] ?? null,
            'request_at'     => $data['request_at'],
        ]);

        return response()->json(['status'=>'ok']);
    }
}
