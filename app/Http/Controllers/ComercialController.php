<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Rules\Base64PngOrNull;

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

        $header = ['ID', 'Nombre Proyecto', 'Nombre Cliente', 'Ubicación', 'Direccion', 'Telefono', 'Opciones'];
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
        $entregables = Entregables::all()->toArray();
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

                'opcion' => ['required', 'boolean'],
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

            if ($data['opcion']) {
                $data['id_estado'] = 2;
            }

            $data['id_usuario_comercial'] = Auth::user()->id;
        
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

}