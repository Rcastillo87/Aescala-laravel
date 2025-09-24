<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Rules\Base64PngOrNull;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\FirmaContratoMail;

use App\Models\Proyecto;
use App\Models\User;
use App\Models\EntregableProye;
use App\Models\Entregables;
use App\Models\Festivos;

class ComercialController extends Controller
{
    public function index( ) 
    {
        $year = date('Y');
        $festivos = new Festivos;
        $festivos->festivos($year);
        $festivos->festivos($year+1);

        $hoy = Carbon::today();
        $title = 'Proyectos con Cupo Reservado';

        $items = Proyecto::when(Request('nombre_proyecto'), function ($query, $nombre_proyecto) { 
            return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
        })
        ->when(Request('nombre_cliente'), function ($query, $nombre_cliente) { 
            return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
        })
        ->whereNull('id_estado')
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends(request()->query());

        $header = ['ID', 'Nombre Proyecto', 'Nombre Cliente', 'Ubicación', 'Direccion', 'Telefono', 'Estado', 'Opciones'];
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);
        return view('comercial.index', compact('title', 'items', 'festivos', 'departamentos', 'header'));
    }

    public function create() 
    {
        $anterior = url()->previous();
        session(['comercial_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        $anterior = url()->previous();
        session(['comercial_url' => $anterior]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        $proyecto = $id ? Proyecto::find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        if(!$id){
            $entregableProye = '';
            $valor = 0;
            $title = 'Crear Proyecto';
            $suma = 0;
        } else {
            $entre = EntregableProye::with('entregable')->where('id_proyecto', $id)->get();
            $suma = $proyecto->aprov_diseno_por + $proyecto->ini_carpinteria_por + $proyecto->ini_enchape_por + $proyecto->ini_griferia_por + $proyecto->entrega_obra_por;
            $entregableProye = '';
            foreach ($entre as $key => $value) {
                $arr = explode('||', $value->tx_entregable);
                $arrItem = '';
                foreach ($arr as $val) {
                    $arrItem .= '<input type="hidden" name="entregables['.$value->id_entregable.'][items][]" value="'.$val.'">';
                }
                $arrTx = implode('</li><li>', $arr);
                $valor = 0;
                $entregableProye .= 
                    '<div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm" data-entregable-id="1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-[#242e68]">opcion 1</h3>
                            <button class="remove-entregable text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div class="text-sm"><span class="font-semibold">Cantidad:</span>'.$value->cantidad.'</div>
                            <div class="text-sm"><span class="font-semibold">Valor Unitario:</span> $ '.number_format($value->valor_total, 2, '.', ',').'</div>
                            <div class="text-sm"><span class="font-semibold">Total:</span> $ '.number_format($value->cantidad*$value->valor_total, 2, '.', ',').'</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <h4 class="font-medium text-sm mb-1">Items:</h4>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                <li>ertfhdfjdyfjh</li>
                            </ul>
                        </div>
                        '.$arrItem.'
                        <input type="hidden" name="entregables['.$value->id_entregable.'][id]" value="'.$value->id_entregable.'">
                        <input type="hidden" name="entregables['.$value->id_entregable.'][cantidad]" value="'.$value->cantidad.'">
                        <input type="hidden" name="entregables['.$value->id_entregable.'][valor]" value="'.$value->valor_total.'">
                    </div>
                    ';
                $valor += $value->valor_total;
            }
            $title = 'Editar Proyecto';
        }
    
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));
        $ciudades = [];
        if ($id && $proyecto) {
            $departamentoIndex = intval($proyecto->departamento);
            $arayDtp = json_decode($departamentos, true);
            if (isset($arayDtp[$departamentoIndex]['ciudades'])) {
                $ciudades = $arayDtp[$departamentoIndex]['ciudades'];
            }
        }
        $entregables = Entregables::with('defaults')->orderBy('nombre_estregable', 'asc')->get()->toArray();
        $tipoDocs = Proyecto::$tipoDocumento;
        return view('comercial.create', compact('title', 'proyecto', 'colaUsers', 'departamentos', 'ciudades', 'tipoDocs', 'entregables', 'entregableProye', 'valor', 'suma'));
    }

    public function save(Request $req)
    {
        try {
            $data = $req->validate([
                'id' => 'nullable|integer',
                'nombre_proyecto' => ['required', 'string', 'max:200', Rule::unique('proyectos')->ignore($req->id, 'id')],
                'departamento' => 'required|integer',
                'ciudad' => 'required|integer',
                'direccion' => ['required', 'string', Rule::unique('proyectos')->ignore($req->id, 'id')],
                'nombre_cliente' => 'required|string|max:100',
                'telefono_cliente' => 'required|string|max:15',
                'cedula_cliente' => 'required|digits_between:6,15',
                'tipo_doc_cliente' => ['required', 'integer', Rule::in(array_keys(Proyecto::$tipoDocumento))],
                'dias_trabajo' => 'required|integer|min:1',
                'area_privada' => 'nullable|integer|min:0',

                "termino_1_por" => 'required|integer|min:0|max:100',
                "termino_2_por" => 'required|integer|min:0|max:100',
                "termino_3_por" => 'required|integer|min:0|max:100',
                "termino_4_por" => 'required|integer|min:0|max:100',
                "termino_5_por" => 'required|integer|min:0|max:100',
                "termino_6_por" => 'required|integer|min:0|max:100',

                'opcion' => ['required', 'integer', Rule::in([0, 1])],
                'por_inicia' => [
                    'nullable',
                    'integer',
                    'min:20',
                    'max:100',
                    function ($attribute, $value, $fail) use ($req) {
                        if ($req->opcion == 1 && is_null($value)) {
                            $fail('El porcentaje de inicio es obligatorio cuando la opción está seleccionada.');
                        }
                    }
                ],
                'img_firma' => [new Base64PngOrNull],

                'entregables' => ['required', 'array', 'min:1'],
                'entregables.*.id' => ['required', 'integer', 'distinct', 'exists:entregables,id'],
                'entregables.*.cantidad' => ['required', 'integer', 'min:1'],
                'entregables.*.valor' => ['required', 'integer', 'min:0'],
                'entregables.*.items' => ['required', 'array'],
                'entregables.*.items.*' => ['string', 'max:255'],
            ]);

            $suma = $req->termino_1_por + $req->termino_2_por + $req->termino_3_por + $req->termino_4_por + $req->termino_5_por + $req->termino_6_por;
            if ($suma !== 100) {
                return response()->json([
                    'errors' => ['total_p_back' => ['La suma de los porcentajes debe ser exactamente 100.']]
                ], 422);
            }    

            if (($data['opcion']==1) && $req->img_firma) {
                $data['id_estado'] = 2;
            }

            $data['id_usuario_comercial'] = Auth::user()->id;
            $data['dias_contrato'] = $req->dias_trabajo;
        
            DB::beginTransaction();
            $datosProyecto = collect($data)
                ->except(['entregables'])
                ->toArray();

            $pro = Proyecto::updateOrCreate(
                ['id' => $req->id],
                $datosProyecto
            );

            if($req->id){
                EntregableProye::where('id_proyecto', $req->id)->delete();
            }

            $entregables = $data['entregables'] ?? [];
            foreach ($entregables as $value) {
                $entrega = new EntregableProye();
                $entrega->id_entregable = $value['id'];
                $entrega->id_proyecto   = $pro->id;
                $entrega->cantidad      = $value['cantidad'];
                $entrega->valor_total   = $value['valor'];
                $entrega->tx_entregable = implode("||", $value['items']);
                $entrega->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => ucfirst($req->id ? 'Proyecto editado con éxito' : 'Proyecto creado con éxito'),
                'redirect' => session('comercial_url')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function firmarContrato($token){

        $data = Crypt::decryptString($token);
        [$id, $cedula] = explode('||', $data);

        $proyecto = Proyecto::with('entreProyecto')->findOrFail($id);
        $dptArray = json_decode(
            file_get_contents(storage_path('json/jsonCityColombia.json')), 
            true
        );
        $ciudad_dpt = $dptArray[$proyecto->departamento]['departamento'] . ', ' .
                    $dptArray[$proyecto->departamento]['ciudades'][$proyecto->ciudad];

        $meses   = ceil($proyecto->dias_trabajo / 24);
        $txMeses = $this->numeroATexto($meses);
        $total   = $proyecto->entreProyecto()
                    ->selectRaw('SUM(valor_total * cantidad) as total')
                    ->value('total');
        $txTotal = $this->numeroATexto($total);

        $valTerm1 = ceil($total * $proyecto->termino_1_por / 100);
        $valTerm2 = ceil($total * $proyecto->termino_2_por / 100);
        $valTerm3 = ceil($total * $proyecto->termino_3_por / 100);
        $valTerm4 = ceil($total * $proyecto->termino_4_por / 100);
        $valTerm5 = ceil($total * $proyecto->termino_5_por / 100);
        $valTerm6 = ceil($total * $proyecto->termino_6_por / 100);

        $carbon = Carbon::parse($proyecto->fec_begin_cont);
        $carbon->locale('es');
        $fechaTexto = $carbon->translatedFormat('d \d\e F \d\e Y');

        // Preparar entregables para la vista
        $entregables = $proyecto->entreProyecto->map(function($e){
            return [
                "cantidad" => $e->cantidad,
                "titulo" => $e->entregable->nombre_estregable,
                "items"  => explode("||", $e->tx_entregable),
                "precio" => number_format($e->valor_total, 0, ',', '.')
            ];
        })->toArray();

        $path = public_path('img/firmaRepre.png');
        if (file_exists($path)) {
            $imageData = file_get_contents($path);
            $imageInfo = getimagesize($path);
            $mime = $imageInfo['mime'];
            $base64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
        } else {
            $base64 = null;
        }

        // Preparar datos para la vista
        $data = [
            "id_proyecto"         => $id,
            "fecha_contrato"      => mb_strtoupper($fechaTexto, 'UTF-8'),
            "nombre_cliente"      => Str::title($proyecto->nombre_cliente),
            "ciudad_dpt"          => $ciudad_dpt,
            "tipo_doc_cliente"    => Proyecto::$tipoDocumento[$proyecto->tipo_doc_cliente][1] ?? '',
            "tipo_doc_cliente_acro" => Proyecto::$tipoDocumento[$proyecto->tipo_doc_cliente][0] ?? '',
            "documento_cliente"   => number_format($proyecto->cedula_cliente, 0, ',', '.'),
            "direccion_proye"     => $proyecto->direccion,
            "area_privada_proye"  => $proyecto->area_privada,
            "dias_proye"          => $proyecto->dias_contrato,
            "meses_proye"         => $meses,
            "tx_meses_proye"      => Str::title($txMeses),
            "tx_valor_total"      => Str::title($txTotal),
            "valor_total"         => number_format($total, 0, ',', '.'),
            "val_term_1"          => number_format($valTerm1, 0, ',', '.'),
            "val_term_2"          => number_format($valTerm2, 0, ',', '.'),
            "val_term_3"          => number_format($valTerm3, 0, ',', '.'),
            "val_term_4"          => number_format($valTerm4, 0, ',', '.'),
            "val_term_5"          => number_format($valTerm5, 0, ',', '.'),
            "val_term_6"          => number_format($valTerm6, 0, ',', '.'),
            "por_term_1"          => $proyecto->termino_1_por,
            "por_term_2"          => $proyecto->termino_2_por,
            "por_term_3"          => $proyecto->termino_3_por,
            "por_term_4"          => $proyecto->termino_4_por,
            "por_term_5"          => $proyecto->termino_5_por,
            "por_term_6"          => $proyecto->termino_6_por,
            "img_firma"           => $proyecto->img_firma,
            "entregables"         => $entregables,
            "dias_trabajo"        => $proyecto->dias_trabajo,
            'imgRepre'            => $base64,
        ];

        return view('auth.firmarContrato', compact('data'));
    }

    public function numeroATexto($numero)
    {
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        return $formatter->format($numero);
    }

    public function guardarFirma(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:proyectos,id',
            'img_firma' => 'required|string',
        ]);

        try {
            $proyecto = Proyecto::findOrFail($request->id);
            if($proyecto->opcion==1){
                $proyecto->id_estado = 2;
            }
            $proyecto->img_firma = $request->img_firma;
            $proyecto->save();

            return response()->json([
                'status' => 'success',
                'message' => 'La firma se guardó correctamente ✅'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar la firma: ' . $e->getMessage()
            ], 500);
        }
    }


    public function sendLinkByEmail(Request $request)
    {
        $request->validate([
            'link' => 'required|url',
            'email' => 'required|email',
            'id' => 'required|exists:proyectos,id'
        ]);

        try {
            $linkContrato = $request->link;
            $proyecto = Proyecto::findOrFail($request->id);
            Mail::to($request->email)->send(
                new FirmaContratoMail($linkContrato, $proyecto->nombre_cliente)
            );
            return response()->json([
                'status'  => 'success',
                'message' => 'El enlace se envió correctamente al correo proporcionado ✅',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al enviar el enlace: ' . $e->getMessage()
            ], 500);
        }
    }

}