<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\FirmaContratoMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Rules\Base64PngOrNull;

use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Area;

class OtrosiController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Otro Si';
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
            ->when(!$usuario->isAdmin, function ($query) use ($usuario) {
                $query->where('id_user_encargado', $usuario->id);
            })
            ->paginate(10)
            ->appends(request()->query());

        $user = User::where('id_rol', 3)->get()->toArray();
        $headers = ['Nombre Proyecto', 'En Cargado', 'Numero', 'Fecha de Creacion', 'Estado', 'Fecha Firma', 'Opciones'];
        return view('otrosi.index', compact( 'title', 'items', 'headers', 'user'));
    }

    public function create() 
    {
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        $item = $id ? Otrosi::with(['proyecto', 'area_entregable.area'])->find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $title = $id ? 'Editar Otro Si' : 'Crear Otro Si';

        $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5])
            ->when(!Auth::user()->isAdmin, function ($query) {
                $query->where(function ($q) {
                    $q->where('id_user', Auth::user()->id)
                    ->orWhere('id_user_obra_blanca', Auth::user()->id)
                    ->orWhere('id_user_carpinteria', Auth::user()->id);
                });
            })
            ->whereNotNull('cedula_cliente')
            ->whereNotNull('tipo_doc_cliente')
            ->orderBy('nombre_proyecto', 'ASC')
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        if(!$id) {
            $itemsOtroSi = '';
        } else {
            $itemsOtroSi = $this->renderHtml($item);
        }

        return view('otrosi.create', compact('title', 'proyectos', 'colaUsers', 'item', 'itemsOtroSi'));
    }

    public function renderHtml($otroSi)
    {
        // Encabezado
        $html = '<div class="bg-[#242e68] text-white p-4 mt-6">
                <h2 class="text-xl text-[#242e68] font-bold">📁 Proyecto: '. e($otroSi->proyecto->nombre_proyecto) .'</h2>
            </div>';

        // Inicio de la tabla
        $html .= '
            <div class="overflow-x-auto w-full border border-gray-200 rounded-b-lg shadow-md">
                <table class="min-w-full w-full text-sm text-left text-gray-700 border-collapse table-auto">
                    <colgroup>
                        <col style="width: 20%;">
                        <col style="width: 50%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                    </colgroup>
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 font-semibold text-[#242e68]">Área</th>
                            <th class="px-4 py-2 font-semibold text-[#242e68]">Material / Actividad</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Cantidad</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Valor Unitario</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        ';

        // Agrupar por id_area para crear filas por Área
        $groups = $otroSi->area_entregable->groupBy('id_area');

        $totalGeneral = 0;
        $hiddenInputs = '';
        // hidden input del proyecto (id_proyecto_excel)
        $hiddenInputs .= '<input type="hidden" name="id_proyecto_excel" value="' . e($otroSi->id_proyecto) . '">';

        foreach ($groups as $idArea => $rows) {
            // nombre del área (si existe relación)
            $first = $rows->first();
            $areaNombre = $first->area->nombre_area ?? 'Área ' . $idArea;

            $subtotalArea = 0;
            $rowCount = $rows->count();
            foreach ($rows as $index => $ent) {
                $cantidad = (int) $ent->cantidad;
                $valor = (int) $ent->valor;
                $subtotal = $cantidad * $valor;
                $subtotalArea += $subtotal;

                $html .= '<tr>';
                if ($index === 0) {
                    $html .= '<td class="px-4 py-2 font-semibold align-top" rowspan="' . $rowCount . '">' . e($areaNombre) . '</td>';
                }
                $html .= '<td class="px-4 py-2">' . e($ent->descripccion) . '</td>';
                $html .= '<td class="px-4 py-2 text-right">' . number_format($cantidad, 0, ',', '.') . '</td>';
                $html .= '<td class="px-4 py-2 text-right">$ ' . number_format($valor, 0, ',', '.') . '</td>';
                $html .= '<td class="px-4 py-2 text-right">$ ' . number_format($subtotal, 0, ',', '.') . '</td>';
                $html .= '</tr>';

                // hidden inputs (estructura: entregables[<id_area>][items][<index>][...])
                // repetimos id_area y area_nombre por cada item — igual que en la versión JS anterior
                $hiddenInputs .= '<input type="hidden" name="entregables[' . $idArea . '][id_area]" value="' . e($idArea) . '">';
                $hiddenInputs .= '<input type="hidden" name="entregables[' . $idArea . '][area_nombre]" value="' . e($areaNombre) . '">';
                $hiddenInputs .= '<input type="hidden" name="entregables[' . $idArea . '][items][' . $index . '][material]" value="' . e($ent->descripccion) . '">';
                $hiddenInputs .= '<input type="hidden" name="entregables[' . $idArea . '][items][' . $index . '][cantidad]" value="' . e($cantidad) . '">';
                $hiddenInputs .= '<input type="hidden" name="entregables[' . $idArea . '][items][' . $index . '][valor_unitario]" value="' . e($valor) . '">';
            }

            // fila subtotal por área
            $html .= '
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="4" class="px-4 py-2 text-right text-[#242e68]">Subtotal ' . e($areaNombre) . '</td>
                    <td class="px-4 py-2 text-right text-[#242e68]">$ ' . number_format($subtotalArea, 0, ',', '.') . '</td>
                </tr>
            ';

            $totalGeneral += $subtotalArea;
        }

        // pie (total general)
        $html .= '
                </tbody>
                <tfoot class="bg-[#f7f9ff] font-bold text-[#242e68] border-t-2 border-[#242e68]">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-lg">Total General</td>
                        <td class="px-4 py-3 text-right text-lg">$ ' . number_format($totalGeneral, 0, ',', '.') . '</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        ';

        // agregar hidden inputs al final del bloque
        $html .= $hiddenInputs;

        // cerrar contenedor
        $html .= '</div>';
        return  $html;
    }

    public function save(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer|exists:otro_si,id',
            'id_proyecto' => 'required|exists:proyectos,id|same:id_proyecto_excel',
            'id_proyecto_excel' => 'required|exists:proyectos,id',
            'plantilla_otro_si' => 'required|file|mimes:xlsx,xls',

            'entregables' => 'required|array|min:1',
            'entregables.*.id_area' => 'required|exists:areas,id',
            'entregables.*.items' => 'required|array|min:1',
            'entregables.*.items.*.material' => 'required|string',
            'entregables.*.items.*.cantidad' => 'required|integer|min:1',
            'entregables.*.items.*.valor_unitario' => 'required|integer|min:1'
        ], [
            'id_proyecto.same' => 'El proyecto seleccionado en el Excel debe coincidir con el proyecto principal.'
        ]);

        DB::beginTransaction();
        try {
            // 📄 Convertir archivo Excel a base64
            $file = $request->file('plantilla_otro_si');
            $filePath = $file->getRealPath();
            $fileContent = file_get_contents($filePath);

            $mimeType = $file->getMimeType();
            $base64Documento = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);

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
            $otroSi->plantilla = $base64Documento;
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
                        'cantidad' => (int) $item['cantidad'],
                        'valor' => (int) $item['valor_unitario']
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

    public function valiPlantilla(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo leer el archivo: ' . $e->getMessage()
            ], 400);
        }

        $nombreProyecto = trim(preg_replace('/\s+/', ' ', $rows[1]['C'] ?? ''));
        $proyecto = Proyecto::whereRaw("
            LOWER(REPLACE(REPLACE(REPLACE(REPLACE(nombre_proyecto, '  ', ' '), '\t', ''), '\n', ''), '\r', '')) = ?
        ", [mb_strtolower($nombreProyecto)])
        ->first();

        if (!$proyecto) {
            $proyecto = Proyecto::whereRaw("
                LOWER(REPLACE(REPLACE(REPLACE(REPLACE(nombre_proyecto, '  ', ' '), '\t', ''), '\n', ''), '\r', '')) LIKE ?
            ", ['%' . mb_strtolower($nombreProyecto) . '%'])
            ->first();
        }
        if (!$proyecto) {
            return response()->json([
                'error' => "El proyecto '$nombreProyecto' no existe en la base de datos."
            ], 400);
        }

        $resp = [
            'nombre_proyecto' => $nombreProyecto,
            'id_proyecto' => $proyecto->id,
            'espacios' => [],
        ];

        $headers = [
            "A" => "ESPACIO",
            "B" => "ITEM",
            "C" => "MATERIAL/ ACTIVIDAD",
            "D" => "CANT",
            "E" => "VALOR UNITARIO",
            "F" => "VALOR TOTAL"
        ];

        unset($rows[1]);
        $espacioActual = null;
        $errores = [];

        foreach ($rows as $index => $row) {
            $valores = array_intersect_key($row, array_flip(['A', 'B', 'C', 'D', 'E', 'F']));
            $valores = array_map(fn($v) => is_string($v) ? trim($v) : $v, $valores);

            // Saltar filas vacías o encabezados
            if ($headers === $valores || empty(array_filter($valores))) {
                continue;
            }

            if (
                (!isset($valores['B']) || trim($valores['B']) === '') &&
                (!isset($valores['E']) || trim($valores['E']) === '') &&
                (!isset($valores['C']) || trim($valores['C']) === '') &&
                (!isset($valores['D']) || trim($valores['D']) === '')
            ) {
                continue;
            }

            // Detectar nuevo ESPACIO
            if (!empty(trim($valores['A'] ?? ''))) {
                // Si ya había un espacio actual, guardarlo antes de iniciar el siguiente
                if ($espacioActual) {
                    $resp['espacios'][] = $espacioActual;
                }

                $nombreArea = trim($valores['A']);
                $area = Area::whereRaw('LOWER(nombre_area) = LOWER(?)', [$nombreArea])->first();

                if (!$area) {
                    $errores[] = "Fila $index: el ESPACIO '$nombreArea' no existe en la tabla áreas.";
                    $espacioActual = [
                        'id_area' => 0,
                        'area_nombre' => $nombreArea,
                        'items' => []
                    ];
                } else {
                    $espacioActual = [
                        'id_area' => $area->id,
                        'area_nombre' => $area->nombre_area,
                        'items' => []
                    ];
                }
            }

            if (!$espacioActual) continue; // sin área actual, se ignora la fila

            // Validar datos del ítem
            $material = $valores['C'] ?? null;
            $cant = (int) $valores['D'] ?? null;
            $valorUnitario = $valores['E'] ?? null;
            $valorUnitario = (int) str_replace(['$', ',', ' '], '', $valorUnitario);

            $erroresFila = [];

            if (empty($material)) {
                $erroresFila[] = 'MATERIAL/ACTIVIDAD vacío.';
            }
            if (!filter_var($cant, FILTER_VALIDATE_INT) || (int)$cant <= 0) {
                $erroresFila[] = 'CANT debe ser un número entero mayor que 0.';
            }
            if (!filter_var($valorUnitario, FILTER_VALIDATE_INT) || (int)$valorUnitario <= 0) {
                $erroresFila[] = 'VALOR UNITARIO debe ser un número entero mayor que 0.';
            }

            $espacioActual['items'][] = [
                'material' => $material,
                'cantidad' => (int)$cant,
                'valor_unitario' => (int)$valorUnitario,
            ];

            if (!empty($erroresFila)) {
                $errores[] = "Fila $index: " . implode(' | ', $erroresFila);
            }
        }

        // Agregar el último espacio pendiente
        if ($espacioActual) {
            $resp['espacios'][] = $espacioActual;
        }

        // ✅ Retornar errores o data validada
        if (!empty($errores)) {
            return response()->json([
                'errores' => $errores
            ], 422);
        }

        return response()->json($resp, 200);
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
        $this->otroSiPdf($id);
    }

    public function sendLinkByEmail(Request $request)
    {
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

}
