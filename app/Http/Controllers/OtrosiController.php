<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Auth;

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

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo leer el archivo: ' . $e->getMessage()
            ], 400);
        }

        // ✅ 1. Nombre del proyecto (primera celda)
        $nombreProyecto = trim($rows[1]['C'] ?? '');
        if (!$nombreProyecto) {
            return response()->json(['error' => 'No se encontró el nombre del proyecto en la primera fila.'], 400);
        }

        $proyecto = Proyecto::whereRaw('LOWER(nombre_proyecto) = LOWER(?)', [$nombreProyecto])->first();
        if (!$proyecto) {
            return response()->json(['error' => "El proyecto '$nombreProyecto' no existe en la base de datos."], 400);
        }

        // ✅ 2. Procesar sectores
        $sectores = [];
        $sectorActual = null;
        $headers = [
            "A" => "ESPACIO",
            "B" => "ITEM",
            "C" => "MATERIAL/ ACTIVIDAD",
            "D" => "CANT",
            "E" => "VALOR UNITARIO",
            "F" => "VALOR TOTAL"
        ];
        unset($rows[1]);
        $espacio = null;

        foreach ($rows as $index => $row) {
            // Convertir valores a string trim
            $valores = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

            if ($headers === $valores) {
                continue;
            }

            if (empty(array_filter($valores))) {
                continue;
            }

            if (
                (!isset($valores['A']) || trim($valores['A']) === '' || is_null($valores['A'])) &&
                (!isset($valores['B']) || trim($valores['B']) === '' || is_null($valores['B'])) &&
                (!isset($valores['C']) || trim($valores['C']) === '' || is_null($valores['C'])) &&
                (!isset($valores['D']) || trim($valores['D']) === '' || is_null($valores['D']))
            ) {
                continue;
            }

            $erroresFila = [];
            if (!empty(trim($valores['A'] ?? ''))) {
                $espacio = $valores['A'];
                $areaExiste = Area::where('nombre_area', $espacio)->exists();
                if (!$areaExiste) {
                    $erroresFila[] = "ESPACIO '$espacio' no existe en la tabla áreas.";
                }
            }

            $material = $valores['B'] ?? null;
            $cant = $valores['C'] ?? null;
            $valorUnitario = $valores['D'] ?? null;

            // MATERIAL/ACTIVIDAD
            if (!$material) {
                $erroresFila[] = 'MATERIAL/ACTIVIDAD no puede ser vacío.';
            }

            // CANT
            if (!is_numeric($cant) || $cant > 0) {
                $erroresFila[] = 'CANT debe ser numérico y mayor que 0.';
            }

            // VALOR UNITARIO
            if (!is_numeric($valorUnitario) || $valorUnitario > 0) {
                $erroresFila[] = 'VALOR UNITARIO debe ser numérico y mayor que 0.';
            }

            if (count($erroresFila) > 0) {
                $sectorActual['errores'][] = [
                    'fila' => $index,
                    'mensaje' => implode(' | ', $erroresFila)
                ];
            } else {
                $sectorActual['filas_validas'][] = [
                    'espacio' => $espacio,
                    'material' => $material,
                    'cant' => (float)$cant,
                    'valor_unitario' => (float)$valorUnitario
                ];
            }
        }

        // Agregar último sector si existe
        if ($sectorActual) {
            $sectores[] = $sectorActual;
        }

        // ✅ Si hay errores, retornarlos
        $erroresTotales = collect($sectores)->pluck('errores')->flatten(1)->filter()->values();
        if ($erroresTotales->isNotEmpty()) {
            return response()->json([
                'proyecto' => $nombreProyecto,
                'errores' => $sectores
            ], 422);
        }

        // ✅ Si no hay errores, retornar la data validada
        return response()->json([
            'proyecto' => $nombreProyecto,
            'sectores' => $sectores
        ]);
    }


}
