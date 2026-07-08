<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\FirmaContratoMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Rules\Base64PngOrNull;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Area;
use App\Models\OtrosiRefe;

class OtrosiController extends Controller
{
    public function index( )
    {
        Gate::authorize('otro_si.index');

        $title = 'Lista de Otrosí';
        $usuario = Auth::user();

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
            ->when(!($usuario->isAdmin || $usuario->isUser), function ($query) use ($usuario) {
                $query->where('id_user_encargado', $usuario->id);
            })
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        $user = User::where('id_rol', 3)->get()->toArray();
        $headers = ['Nombre Proyecto', 'En Cargado', 'Numero', 'Fecha de Creacion', 'Estado', 'Fecha Firma', 'Opciones'];
        return view('otrosi.index', compact( 'title', 'items', 'headers', 'user'));
    }

    public function create()
    {
        Gate::authorize('otro_si.create');
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        Gate::authorize('otro_si.edit');
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        try {
            $item = $id ? Otrosi::with(['proyecto', 'area_entregable.area'])->find($id) : null;
            $colaUsers = User::where('id_rol', 3)
                ->where('activo', 1)
                ->get(['id', 'nombre_completo'])
                ->toArray();

            $title = $id ? 'Editar Otrosi' : 'Crear Otrosi';

            $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5, 2])
                ->when(!Auth::user()->isAdmin, function ($query) {
                    $query->where(function ($q) {
                        $q->where('id_user', Auth::user()->id)
                        ->orWhere('id_user_obra_blanca', Auth::user()->id)
                        ->orWhere('id_user_diseno', Auth::user()->id);
                    });
                })
                ->whereNotNull('cedula_cliente')
                ->whereNotNull('tipo_doc_cliente')
                ->orderBy('nombre_proyecto', 'ASC')
                ->get(['id', 'nombre_proyecto'])
                ->toArray();

            $areas = Area::get(['id', 'nombre_area'])->toArray();
            $unidades = Otrosi::$unidades;

            $entregables = [];
            if ($item) {
                $entregables = $item->area_entregable
                    ->groupBy('id_area')
                    ->map(function ($group) {
                        return [
                            'id_area' => $group->first()->id_area,
                            'areaText' => $group->first()->area->nombre_area,
                            'items' => $group->map(function ($item) {
                                return [
                                    'material' => $item->descripccion,
                                    'cantidad' => $item->cantidad,
                                    'valor_unitario' => $item->valor,
                                    'unidad' => $item->unidad,
                                ];
                            })->values()
                        ];
                    })->values();
            }

            return view('otrosi.create', compact('title', 'proyectos', 'colaUsers', 'item', 'areas', 'entregables', 'unidades'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
        Gate::authorize('otro_si.save');

        $request->validate([
            'id' => 'nullable|integer|exists:otro_si,id',
            'id_proyecto' => 'required|exists:proyectos,id',
            'entregables' => 'required|array|min:1',
            'entregables.*.id_area' => 'required|exists:areas,id',
            'entregables.*.items' => 'required|array|min:1',
            'entregables.*.items.*.material' => 'required|string',
            'entregables.*.items.*.cantidad' => 'required|numeric|min:0',
            'entregables.*.items.*.valor_unitario' => 'required|integer',
            'entregables.*.items.*.unidad' => ['required','integer', Rule::in(array_keys(Otrosi::$unidades))],
        ]);

        DB::beginTransaction();
        try {

            if($request->id){
                $otroSi = Otrosi::find($request->id);
                $otroSi->area_entregable()->delete();
                $msg = 'actualizado';
                $otroSi->estado = 0;
                $otroSi->sugerencia_cliente = null;

            } else {
                $otroSi = new Otrosi();
                $ultimoNumero = Otrosi::where('id_proyecto', $request->id_proyecto)->max('numero');
                $nuevoNumero = $ultimoNumero ? $ultimoNumero + 1 : 1;
                $otroSi->numero = $nuevoNumero;
                $otroSi->fecha_creacion = now();
                $otroSi->id_user_encargado = Auth::id();
                $msg = 'creado';
            }

            // 🧾 Crear registro principal
            $otroSi->id_proyecto = $request->id_proyecto;
            $otroSi->save();

            // 📦 Guardar ítems relacionados
            $itemsInsert = [];
            foreach ($request->entregables as $esp) {
                $idArea = $esp['id_area'];
                foreach ($esp['items'] as $item) {
                    $itemsInsert[] = [
                        'id_area' => $idArea,
                        'id_otro_si' => $otroSi->id,
                        'descripccion' => $item['material'],
                        'cantidad' => (double) $item['cantidad'],
                        'valor' => (int) $item['valor_unitario'],
                        'unidad' => (int) $item['unidad']
                    ];
                }
            }

            if (!empty($itemsInsert)) {
                DB::table('area_entregables')->insert($itemsInsert);
            }

            DB::commit();
            return redirect()->route('otro_si.index')->with('success', 'Otro Sí '. $msg .' correctamente');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['db' => 'Error en la base de datos: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Error inesperado: ' . $e->getMessage()]);
        }
    }

    public function otroSiPdf($id)
    {
        try {
            $otroSi = Otrosi::with(['proyecto', 'user_encargado', 'area_entregable', 'area_entregable.area'])->findOrFail($id);
            $data = $otroSi->otroSi;
            $pdf = PDF::loadView('otrosi.pdfOtroSi', $data);
            return $pdf->stream('otro-si-' . $otroSi->id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generando OTRO SÍ PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function firmarOtroSi($token)
    {
        $data = Crypt::decryptString($token);
        [$id, $numero, $fecha_creacion] = explode('||', $data);

        $otroSi = Otrosi::with(['proyecto', 'user_encargado', 'area_entregable', 'area_entregable.area'])->findOrFail($id);
        $data = $otroSi->otroSi;
        return view('auth.firmarOtroSi', compact('data', 'otroSi'));
    }

    public function guardarFirmaOtroSi(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id_otro_si' => 'required|exists:otro_si,id',
                'firma_base64' => [new Base64PngOrNull],
                'sugerencia_cliente' => 'nullable|string',
                'estado' => 'required|in:1,2'
            ]);

            $otroSi = Otrosi::findOrFail($request->id_otro_si);

            if($request->estado==1){
                $otroSi->img_firma = $request->firma_base64;
                $otroSi->fecha_firma = now();
                $smg = 'Firma guardada correctamente.';
            } else {
                $otroSi->sugerencia_cliente = $request->sugerencia_cliente;
                $smg = 'Sugerencia guardada correctamente.';
            }

            $otroSi->estado = $request->estado;
            $otroSi->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $smg
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function otroSiPdfPublic($id)
    {
        return $this->otroSiPdf($id);
    }

    public function sendLinkByEmail(Request $request)
    {
        Gate::authorize('otro_si.index');
        $request->validate([
            'link' => 'required|url',
            'email' => 'required|email',
            'id' => 'required|exists:otro_si,id'
        ]);

        try {
            $linkContrato = $request->link;
            $otrosi = Otrosi::with('proyecto')->findOrFail($request->id);
            Mail::to($request->email)->send(
                new FirmaContratoMail($linkContrato, $otrosi->proyecto->nombre_cliente, 'Firmar de Otro Sí')
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

    public function saveRefe(Request $request)
    {
        Gate::authorize('cartera.save');
        $val = [
            'id_proyecto'  => ['required', 'integer', Rule::exists('proyectos', 'id')],
            'valor_referecia' => [ 'required', 'integer', 'min:1'],
            'referencia' => [ 'required', 'integer', 'min:1'],
            'numero' => [ 'required', 'integer', 'min:1'],
        ];

        $validator = Validator::make($request->all(), $val, [
            'required' => 'Este campo es obligatorio.',
            'integer'  => 'Debe ser un número válido.',
            'min'      => 'El valor debe ser mayor a 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Hay errores en el formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $otrosi = Otrosi::where(['id_proyecto' => $request->id_proyecto, 'numero' => $request->numero])->first();
            OtrosiRefe::create([
                'id_otro_si'  => $otrosi->id,
                'valor' => $request->valor_referecia,
                'referencia' => $request->referencia,
                'id_user'      => Auth::id(),
            ]);

            DB::commit();
            return response()->json([
                'status'  => true,
                'url' => route('cartera.index', $request->id_proyecto),
                'message' => 'Pago registrado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al registrar el pago.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
