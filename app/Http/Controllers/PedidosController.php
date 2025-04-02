<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Pedidos;
use App\Models\Proveedor;
use App\Models\User;


class PedidosController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Pedidos';
        $items = Pedidos::with(['proveedor', 'material'])
        ->selectRaw('id_factura, 
                    DATE(fecha) as fecha,
                    id_proveedor, 
                    SUM(cantidad * vr_unidad) as total,
                    COUNT(*) as items')
        ->when(request('factura'), function ($query, $factura) {
            return $query->where('id_factura', 'like', "%{$factura}%");
        })
        ->when(request('fecha_desde'), function ($query, $fecha) {
            return $query->whereDate('fecha', '>=', $fecha);
        })
        ->when(request('fecha_hasta'), function ($query, $fecha) {
            return $query->whereDate('fecha', '<=', $fecha);
        })
        ->when(request('proveedor'), function ($query, $proveedor) {
            return $query->whereHas('proveedor', function ($q) use ($proveedor) {
                $q->where('razon_social', 'like', "%{$proveedor}%");
            });
        })
        ->groupBy('id_factura', 'id_proveedor', DB::raw('DATE(fecha)'))
        ->orderBy('fecha', 'desc')
        ->paginate(10)
        ->through(function ($factura) {
            return [
                'factura' => $factura->id_factura,
                'proveedor' => $factura->proveedor->razon_social ?? 'N/A',
                'total' => $factura->total,
                'items' => $factura->items,
                'fecha' => $factura->fecha
            ];
        });
        
        $headers = ['ID Factura y/o Orden', 'Proveedor', 'Fecha Pedido', 'Cantidad de Items', 'Total', 'Opciones'];
        return view('pedidos.index', compact('title', 'items', 'headers'));
    }

    public function create( ) 
    {
        return $this->form();
    }

    public function edit($id) 
    {
        return $this->form($id);
    }

    public function form($id = null)
    {
        $herra = $id?Herramienta::find($id):null;
        $title = $id?'Editar Herramienta':'Crear Herramienta';
        $estado = Herramienta::$estado;
        return view('herramienta.create', compact('title', 'herra', 'estado'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_herramienta' => 'required|string|max:200',
            'referencia' => 'required|string|max:50',
            'marca' => 'required|string|max:50',
            'observacion' => 'nullable|string',
            'estado' => ['required', 'integer', Rule::in(array_keys(Herramienta::$estado))],
        ]);
    
        $msg = ucfirst($req->id ? 'herramienta editado con éxito' : 'herramienta creado con éxito');
    
        try {
            DB::beginTransaction();
            Herramienta::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
    
            return redirect()->route('herramienta.index')->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function listPrestamos()
    {
        try {
            $lisPrestamos = HerramientaPrestamo::with('user')
                ->where('id_herramienta', request('id'))
                ->orderBy('id', 'desc')
                ->paginate(10);

            $lastPrestamo = HerramientaPrestamo::where('id_herramienta', request('id'))
                ->orderBy('id', 'desc')->first();
    
            return response()->json([
                'status' => true,
                'message' => 'Lista de préstamos.',
                'data' => $lisPrestamos,
                'last' => $lastPrestamo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista de préstamos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function savePrestamo(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'id_herramienta' => [
                'required',
                'integer',
                Rule::exists('herramientas', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'tipo_prestamo' => ['required', 'integer', Rule::in(array_keys(HerramientaPrestamo::$prestamo))],
            'observacion' => 'required|string|max:255',
            'fec_prestamo' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today']
        ]);

        $lastPrestamo = HerramientaPrestamo::where('id_herramienta', $data['id_herramienta'])->orderBy('id', 'desc')->first();
        if($lastPrestamo){
            if( $lastPrestamo->tipo_prestamo == $data['tipo_prestamo']){
                return back()->with('error', "El dispositivo se encuentra " . HerramientaPrestamo::$prestamo[$lastPrestamo->tipo_prestamo]);
            }
            /*if(($lastPrestamo->id_user != $data['id_user']) && ($lastPrestamo->tipo_prestamo==2)){
                return back()->with('error', "El dispositivo lo posee ".$lastPrestamo->user->nombre_completo);
            }*/
        }
        
        $msg = ucfirst($req->id ? "Editado con éxito" : 'Creado con éxito');
    
        try {
            DB::beginTransaction();
            HerramientaPrestamo::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
            return redirect()->route('herramienta.index')->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
    
}