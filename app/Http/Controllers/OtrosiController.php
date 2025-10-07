<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Area;
use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;

class OtrosiController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Otro Si';
        $items = Otrosi::with(['proyecto', 'user_encargado'])
        ->whereHas('proyecto', function ($query) {
            if (request('nombre_proyecto')) {
                $query->where('nombre_proyecto', 'like', '%' . request('nombre_proyecto') . '%');
            }
        })
        ->whereHas('user_encargado', function ($query) {
            if (request('id_userSerch')) {
                $query->where('id', request('id_userSerch'));
            }
        })
        ->paginate(10)
        ->appends(request()->query());

        $user = User::where('id_rol', 3)->get()->toArray();
        $headers = ['Nombre Proyecto', 'En Cargado', 'Numero', 'Fecha de Creacion', 'Opciones'];
        return view('otrosi.index', compact( 'title', 'items', 'headers', 'user'));
    }

    public function create( ) 
    {

    }

    public function otroSiPdf($id)
    {
        try {
            $otroSi = Otrosi::with(['proyecto', 'user_encargado', 'area_entregable', 'area_entregable.area'])->findOrFail($id);
            $dptArray = json_decode(
                file_get_contents(storage_path('json/jsonCityColombia.json')), 
                true
            );

            $ciudad_dpt = $dptArray[$otroSi->proyecto->departamento]['departamento'] . ', ' .
                        $dptArray[$otroSi->proyecto->departamento]['ciudades'][$otroSi->proyecto->ciudad];

            // Calcular totales
            $subtotal = $otroSi->area_entregable()
                        ->selectRaw('SUM(valor * cantidad) as subtotal')
                        ->value('subtotal');
            $valorTotal = $subtotal; // acá podrías sumar IVA si aplica
            $txTotal = $this->numeroATexto($valorTotal);

            // Preparar adicionales
            $adicionales = $otroSi->area_entregable;
            /*$otroSi->area_entregable->map(function($a){
                return [
                    "espacio"       => $a->area->nombre_area,
                    "descripcion"   => $a->descripccion,
                    "cantidad"      => $a->cantidad,
                    "valor_unitario"=> number_format($a->valor, 0, ',', '.'),
                    "valor_total"   => number_format($a->valor * $a->cantidad, 0, ',', '.'),
                ];
            })->toArray();*/

            $arr = [];
            foreach ($adicionales as $item) {
                $area = $item->area->nombre_area;
                if (!isset($arr[$area])) {
                    $arr[$area] = [
                        "espacio"       => $area,
                        "items"         => [],
                        "subtotal"      => 0
                    ];
                }
                $arr[$area]["items"][] = [
                    "descripcion"   => $item->descripccion,
                    "cantidad"      => $item->cantidad,
                    "valor_unitario"=> number_format($item->valor, 0, ',', '.'),
                    "valor_total"   => number_format($item->valor * $item->cantidad, 0, ',', '.'),
                ];
                $arr[$area]["subtotal"] += $item->valor * $item->cantidad;
            }


            $carbon = Carbon::now()->locale('es');
            $fechaOtroSi = $carbon->translatedFormat('d \d\e F \d\e Y');

            $path = public_path('img/firmaRepre.png');
            $base64 = null;
            if (file_exists($path)) {
                $imageData = file_get_contents($path);
                $imageInfo = getimagesize($path);
                $mime = $imageInfo['mime'];
                $base64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
            }

            $data = [
                "id_proyecto"       => $otroSi->id,
                "num_otro_si"       => $otroSi->numero,
                "fecha_otro_si"     => mb_strtoupper($fechaOtroSi, 'UTF-8'),
                "nombre_cliente"    => Str::title($otroSi->proyecto->nombre_cliente),
                "tipo_doc_cliente"  => Proyecto::$tipoDocumento[$otroSi->proyecto->tipo_doc_cliente][1] ?? '',
                "tipo_doc_cliente_acro" => Proyecto::$tipoDocumento[$otroSi->proyecto->tipo_doc_cliente][0] ?? '',
                "documento_cliente" => number_format($otroSi->proyecto->cedula_cliente, 0, ',', '.'),
                "ciudad_dpt"        => $ciudad_dpt,
                "adicionales"       => $arr,
                "subtotal"          => number_format($subtotal, 0, ',', '.'),
                "valor_total"       => number_format($valorTotal, 0, ',', '.'),
                "img_firma"         => '',
                "imgRepre"          => $base64,
                "nombre_proyecto" => $otroSi->proyecto->nombre_proyecto
            ];

            $pdf = PDF::loadView('otrosi.pdfOtroSi', $data);
            return $pdf->stream('otro-si-' . $otroSi->id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generando OTRO SÍ PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function numeroATexto($numero)
    {
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        return $formatter->format($numero);
    }

}
