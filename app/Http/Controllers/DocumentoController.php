<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DocumentoController extends Controller
{
    /**
     * Guarda o actualiza el soporte de un registro.
     */
    public function save(Request $request)
    {
        // 1. Validar que no llegue vacío y cumpla los formatos
        $request->validate([
            'archivo'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024', // Máx 10MB
            'id_tabla'    => 'required|string', // Ej: App\Models\Pagos
            'id_registro' => 'required|integer',
        ]);

        try {
            $file = $request->file('archivo');

            // 2. Comprimir el binario al máximo nivel (9)
            // Esto reduce drásticamente el tamaño de los PDFs
            $contenidoComprimido = gzencode(file_get_contents($file->getRealPath()), 9);

            // 3. Guardar o reemplazar usando la relación polimórfica
            Documento::updateOrCreate(
                [
                    'documentable_type' => $request->id_tabla,
                    'documentable_id'   => $request->id_registro,
                ],
                [
                    'nombre'    => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'contenido' => $contenidoComprimido,
                ]
            );

            return back()->with('success', 'Documento guardado con éxito.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Muestra o descarga el archivo desde el binario de la BD.
     */
    public function view($id)
    {
        $documento = Documento::findOrFail($id);

        // 1. Descomprimir el contenido usando el Accesor del modelo
        // o directamente con gzdecode si no usas el modelo
        $contenido = gzdecode($documento->contenido);

        if (!$contenido) {
            return abort(404, 'No se pudo descomprimir el archivo.');
        }

        // 2. Retornar la respuesta con el MIME Type correcto
        return Response::make($contenido, 200, [
            'Content-Type' => $documento->mime_type,
            'Content-Disposition' => 'inline; filename="' . $documento->nombre . '"',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    /**
     * Eliminar un soporte específico.
     */
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->delete();

        return back()->with('success', 'Soporte eliminado correctamente.');
    }
}
