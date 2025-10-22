<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

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
        // Validar que venga un archivo
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Archivo no válido o ausente',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Leer encabezados de la primera fila
            $expectedHeaders = [
                'Area',
                'MATERIAL/ACTIVIDAD',
                'Cantidad',
                'Valor Unitario',
                'Total'
            ];

            $foundHeaders = [];
            foreach ($sheet->getColumnIterator() as $column) {
                $cell = $sheet->getCell($column->getColumnIndex() . '1')->getValue();
                $foundHeaders[] = trim(preg_replace('/\s+/', ' ', (string) $cell));
            }

            // Validar encabezados (ignorando espacios múltiples)
            $missingHeaders = array_diff($expectedHeaders, $foundHeaders);
            if (!empty($missingHeaders)) {
                return response()->json([
                    'message' => 'Error de estructura: faltan columnas',
                    'missing_columns' => array_values($missingHeaders)
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Validar contenido (por ejemplo: filas vacías o valores no numéricos)
            $rowErrors = [];
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowIndex = $row->getRowIndex();
                $area = trim((string) $sheet->getCell('A' . $rowIndex)->getValue());
                $actividad = trim((string) $sheet->getCell('B' . $rowIndex)->getValue());
                $cantidad = $sheet->getCell('C' . $rowIndex)->getValue();
                $valorUnitario = $sheet->getCell('D' . $rowIndex)->getValue();
                $total = $sheet->getCell('E' . $rowIndex)->getValue();

                if ($area === '' || $actividad === '' || !is_numeric($cantidad) || !is_numeric($valorUnitario) || !is_numeric($total)) {
                    $rowErrors[] = [
                        'fila' => $rowIndex,
                        'detalle' => 'Campos vacíos o valores no numéricos detectados.'
                    ];
                }
            }

            if (!empty($rowErrors)) {
                return response()->json([
                    'message' => 'Errores encontrados en los datos.',
                    'errors' => $rowErrors,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Si todo está bien
            return response()->json([
                'message' => 'El archivo es válido.',
                'status' => 'ok'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el archivo.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
