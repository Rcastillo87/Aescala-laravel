<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Georreferencia;
use Illuminate\Validation\Rule;
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
            'request_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'network_type' => ['nullable', 'integer', Rule::in(array_keys(Georreferencia::$networkTypes))],
            'network_generation' => ['nullable', 'integer', Rule::in(array_keys(Georreferencia::$networkGenerations))],
            'signal_dbm' => 'nullable|integer|between:-120,-20',
            'signal_level' => ['nullable', 'integer', Rule::in(array_keys(Georreferencia::$signalLevels))],
            'tipo' => ['nullable', 'integer', Rule::in(array_keys(Georreferencia::$tipos))],
        ]);

        Georreferencia::create([
            'device_id'      => $data['device_id'],
            'lat'            => $data['lat'],
            'lng'            => $data['lng'],
            'accuracy'       => $data['accuracy'] ?? null,
            'battery'        => $data['battery'] ?? null,
            'request_at'     => $data['request_at'],
            'network_type' => $data['network_type'] ?? null,
            'network_generation' => $data['network_generation'] ?? null,
            'signal_dbm' => $data['signal_dbm'] ?? null,
            'signal_level' => $data['signal_level'] ?? null,
            'tipo' => $data['tipo'] ?? null,
        ]);

        return response()->json(['status'=>'ok']);
    }

}
