<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;

use App\Models\User;
use App\Models\Area;
use App\Models\Proyecto;
use App\Models\Otrosi;

class UserController extends Controller
{

    public function index()
    {
        Gate::authorize('user.index');

        $title  = 'Lista de Usuarios';
        $roles  = User::$roles;
        $estado = User::$estado;
        $perPage = request('per_page', 10);

        $items = User::with(['proyectos', 'proyectos.tareas'])
            ->when(request('nombre_completo'), fn ($q, $v) =>
                $q->whereRaw('LOWER(nombre_completo) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('email'), fn ($q, $v) =>
                $q->whereRaw('LOWER(email) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('activo'), fn ($q, $v) =>
                $q->where('activo', $v)
            )
            ->when(request('cedula'), fn ($q, $v) =>
                $q->whereRaw('LOWER(cedula) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('telefono'), fn ($q, $v) =>
                $q->whereRaw('LOWER(telefono) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('id_rol'), fn ($q, $v) =>
                $q->where('id_rol', $v)
            )
            ->paginate($perPage)
            ->withQueryString();

        /** columnas del componente */
        $columns = [
            'nombre',
            'documento',
            'email',
            'telefono',
            'perfil',
            'estado',
            'acciones',
        ];

        /** headers con diseño */
        $headers = [
            'nombre'     => 'Nombre Completo',
            'documento'  => 'Documento',
            'email'      => 'Correo',
            'telefono'   => 'Teléfono',
            'perfil'     => 'Perfil',
            'estado'     => 'Estado',
            'acciones'   => 'Opciones',
        ];

        return view('user.index', compact(
            'title',
            'roles',
            'estado',
            'items',
            'columns',
            'headers'
        ));
    }

    public function create( )
    {
        Gate::authorize('user.create');

        $user = null;
        $title = 'Crear Usuarios';
        $action = 'Crear';
        $roles = User::$roles;
        $tipoDocs = User::$tipoDocumento;
        return view('user.create', compact('roles', 'title', 'action', 'tipoDocs', 'user'));
    }

    public function edit($id)
    {
        Gate::authorize('user.edit');

        $user = User::find($id);
        $title = 'Editar Usuarios';
        $action = 'Editar';
        $roles = User::$roles;
        $tipoDocs = User::$tipoDocumento;
        return view('user.create', compact('roles', 'title', 'action', 'tipoDocs', 'user'));
    }

    public function save(Request $req)
    {
        Gate::authorize('user.save');

        $req->validate([
            'id' => 'nullable|integer',
            'nombre_completo' => 'required|string|max:200',
            'email' => [
                'required',
                'email',
                'max:200',
                Rule::unique('users', 'email')->ignore($req->id)
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],//'nullable|string|min:6|max:200',
            'cedula' => [
                'required',
                'digits_between:6,15',
                Rule::unique('users', 'cedula')->ignore($req->id)
            ],
            'telefono' => 'required|digits_between:7,16',
            'tipo_documento' => ['required', 'integer', Rule::in(array_keys(User::$tipoDocumento))],
            'id_rol' => ['required', 'integer', Rule::in(array_keys(User::$roles))],
        ]);

        $user = [
            'nombre_completo' => $req->nombre_completo,
            'email' => $req->email,
            'cedula' => $req->cedula,
            'telefono' => $req->telefono,
            'tipo_documento' => $req->tipo_documento,
            'direccion' => $req->direccion,
            'id_rol' => $req->id_rol,
            'activo' => 1,
        ];

        // valido los password para los distincos casos
        if (!$req->id) {
            $req->validate([
                'password' => 'required|string|min:6|max:20'
            ]);
            $user['password'] = Hash::make($req->password);
            $msg = 'Usuario creado con éxito';
        } else {
            $req->validate([
                'password' => 'nullable|string|min:6|max:20'
            ]);
            if ($req->password) {
                $user['password'] = Hash::make($req->password);
            }
            $msg = 'Usuario editado con éxito';
        }

        try {
            DB::beginTransaction();
            User::updateOrCreate(
                ['id' => $req->id],
                $user
            );
            DB::commit();
            return redirect()->route('user.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function editStatus($id)
    {
        Gate::authorize('user.editStatus');
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update(['activo' => ($user->activo == 1) ? 2 : 1]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado correctamente.'
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function selectUser()
    {
        $users = User::where('activo', 1)->whereIn('id_rol', [10, 1])
            ->select('id', 'nombre_completo')
            ->get();

        $areas = Area::get(['id', 'nombre_area'])->toArray();
        $unidades = Otrosi::$unidades;

        return response()->json([ 'users' => $users, 'areas' => $areas, 'unidades' => $unidades ], 200);
    }

    public function selectProyecto($id)
    {
        $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5, 2])
            ->where(function ($q) use ($id) {
                $q->where('id_user', $id)
                ->orWhere('id_user_obra_blanca', $id)
                ->orWhere('id_user_diseno', $id);
            })
            ->whereNotNull('cedula_cliente')
            ->whereNotNull('tipo_doc_cliente')
            ->orderBy('nombre_proyecto', 'ASC')
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        return response()->json($proyectos, 200);
    }

    public function saveOtrosi(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'nullable|integer|exists:users,id',
            'id_proyecto' => 'required|exists:proyectos,id',
            'entregables' => 'required|array|min:1',
            'entregables.*.id_area' => 'required|exists:areas,id',
            'entregables.*.items' => 'required|array|min:1',
            'entregables.*.items.*.material' => 'required|string',
            'entregables.*.items.*.cantidad' => 'required|numeric|min:0',
            'entregables.*.items.*.valor_unitario' => 'required|integer',
            'entregables.*.items.*.unidad' => [
                'required',
                'integer',
                Rule::in(array_keys(Otrosi::$unidades))
            ],
        ]);

        DB::beginTransaction();

        try {

            $otroSi = new Otrosi();
            $ultimoNumero = Otrosi::where('id_proyecto', $validated['id_proyecto'])->max('numero');
            $otroSi->numero = $ultimoNumero ? $ultimoNumero + 1 : 1;
            $otroSi->fecha_creacion = now();
            $otroSi->id_user_encargado = $validated['id_user'] ?? null;
            $otroSi->id_proyecto = $validated['id_proyecto'];
            $otroSi->save();
            $itemsInsert = [];
            foreach ($validated['entregables'] as $entregable) {
                foreach ($entregable['items'] as $item) {
                    $itemsInsert[] = [
                        'id_area'       => $entregable['id_area'],
                        'id_otro_si'    => $otroSi->id,
                        'descripccion'  => $item['material'],
                        'cantidad'      => (float) $item['cantidad'],
                        'valor'         => (int) $item['valor_unitario'],
                        'unidad'        => (int) $item['unidad'],
                    ];
                }
            }

            if (!empty($itemsInsert)) {
                DB::table('area_entregables')->insert($itemsInsert);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Otro Sí creado correctamente.',
                'data' => [
                    'id' => $otroSi->id,
                    'numero' => $otroSi->numero,
                    'id_proyecto' => $otroSi->id_proyecto,
                ]
            ], 201);
            
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
}
