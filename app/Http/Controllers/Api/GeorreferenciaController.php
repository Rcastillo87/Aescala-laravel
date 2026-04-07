<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Georreferencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GeorreferenciaController extends Controller
{
    use ApiResponse;

    public function location(Request $request)
    {
        $data = $request->validate([
            'device_id' => 'required|integer|exists:dispositivo,id',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:1000',
            'battery'  => 'nullable|integer|min:0|max:100',
            'request_at' => 'required|date_format:Y-m-d H:i:s',
            'network_type' => [
                'nullable',
                'integer',
                Rule::in(array_keys(Georreferencia::$networkTypes))
            ],
            'network_generation' => [
                'nullable',
                'integer',
                Rule::in(array_keys(Georreferencia::$networkGenerations))
            ],
            'signal_dbm' => 'nullable|integer|between:-120,-20',
            'signal_level' => [
                'nullable',
                'integer',
                Rule::in(array_keys(Georreferencia::$signalLevels))
            ],
            'tipo' => [
                'nullable',
                'integer',
                Rule::in(array_keys(Georreferencia::$tipos))
            ],
            'app_version' => 'nullable|string|max:10',
        ]);

        $minVersion = env('APP_MIN_VERSION');
        $needsUpdate = false;

        if (!empty($data['app_version'])) {
            $needsUpdate = version_compare($data['app_version'], $minVersion, '<');
        }

        DB::beginTransaction();
        try {
            $geo = Georreferencia::create($data);
            DB::commit();
            return $this->success([
                'id' => $geo->id
            ], $needsUpdate ? 'Actualización recomendada' : 'Ubicación registrada');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
