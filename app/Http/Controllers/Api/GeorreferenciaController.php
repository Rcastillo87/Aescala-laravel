<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Georreferencia;
use Illuminate\Http\Request;

class GeorreferenciaController extends Controller
{
    public function location(Request $request)
    {
        $data = $request->validate([
            'device_id' => 'required|integer|exists:dispositivo,id',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:1000',
            'battery'  => 'nullable|integer|min:0|max:100',
            'request_at' => 'required|string',
        ]);

        Georreferencia::create([
            'device_id'      => $data['device_id'],
            'lat'            => $data['lat'],
            'lng'            => $data['lng'],
            'accuracy'       => $data['accuracy'] ?? null,
            'battery'        => $data['battery'] ?? null,
            'request_at'     => $data['request_at'],
        ]);

        return response()->json(['status'=>'ok']);
    }

}
