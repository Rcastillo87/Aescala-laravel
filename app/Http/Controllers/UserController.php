<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Gate;

use App\Models\User;

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
}
