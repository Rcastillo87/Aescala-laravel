<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $otroSi = $id ? Otrosi::find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $title = $id ? 'Editar Otro Si' : 'Crear Otro Si';

        $proyectos = Proyecto::whereIN('id_estado', [1, 3, 5])
            ->when(!Auth::user()->isAdmin, function ($query) {
                $query->where('id_user', Auth::user()->id);
            })
            ->get(['id', 'nombre_proyecto'])->toArray();

        $item = '';
        return view('otrosi.create', compact('title', 'proyectos', 'colaUsers', 'item'));
    }


    public function save(Request $request)
    {
        $request->validate([
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

            // 🔢 Calcular número consecutivo
            $ultimoNumero = Otrosi::where('id_proyecto', $request->id_proyecto)->max('numero');
            $nuevoNumero = $ultimoNumero ? $ultimoNumero + 1 : 1;

            // 🧾 Crear registro principal
            $otroSi = new Otrosi();
            $otroSi->id_proyecto = $request->id_proyecto;
            $otroSi->id_user_encargado = Auth::id();
            $otroSi->numero = $nuevoNumero;
            $otroSi->fecha_creacion = now();
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

            return redirect()->route('otro_si.index')->with('success', 'Otro Sí creado correctamente');

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
                    dd($valores, $headers);
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

}
