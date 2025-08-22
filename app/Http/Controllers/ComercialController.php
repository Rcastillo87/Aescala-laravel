<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
    
        $title = $id ? 'Editar Proyecto' : 'Crear Proyecto';
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
        return view('comercial.create', compact('title', 'proyecto', 'colaUsers', 'departamentos', 'ciudades', 'tipoDocs', 'entregables'));
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

                "aprov_diseno_por"    => 'required|integer|min:0|max:100',
                "ini_carpinteria_por" => 'required|integer|min:0|max:100',
                "ini_enchape_por"     => 'required|integer|min:0|max:100',
                "ini_griferia_por"    => 'required|integer|min:0|max:100',
                "entrega_obra_por"    => 'required|integer|min:0|max:100',

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

                'entregables' => ['required', 'array', 'min:1'],
                'entregables.*.id' => ['required', 'integer', 'distinct', 'exists:entregables,id'],
                'entregables.*.cantidad' => ['required', 'integer', 'min:1'],
                'entregables.*.valor' => ['required', 'integer', 'min:0'],
                'entregables.*.items' => ['required', 'array'],
                'entregables.*.items.*' => ['string', 'max:255'],
            ]);

            $suma = $req->aprov_diseno_por + $req->ini_carpinteria_por + $req->ini_enchape_por + $req->ini_griferia_por + $req->entrega_obra_por;
            if ($suma !== 100) {
                return response()->json([
                    'errors' => ['total_p' => ['La suma de los porcentajes debe ser exactamente 100.']]
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