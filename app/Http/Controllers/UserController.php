<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

use App\Models\User;

class UserController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Usuarios';
        $roles = User::$roles;
        $estado = User::$estado;
        $items = User::with(['proyectos', 'proyectos.tareas'])->when(Request('nombre_completo'), function ($query, $nombre_completo) { 
            return $query->whereRaw('LOWER(nombre_completo) LIKE LOWER(?)', ["%$nombre_completo%"]);
        })
        ->when(Request('email'), function ($query, $email) { 
            return $query->whereRaw('LOWER(email) LIKE LOWER(?)', ["%$email%"]);
        })
        ->when(Request('activo'), function ($query, $activo) { 
            return $query->where('activo', $activo);
        })
        ->when(Request('cedula'), function ($query, $cedula) { 
            return $query->whereRaw('LOWER(cedula) LIKE LOWER(?)', ["%$cedula%"]);
        })
        ->when(Request('telefono'), function ($query, $telefono) { 
            return $query->whereRaw('LOWER(telefono) LIKE LOWER(?)', ["%$telefono%"]);
        })
        ->when(Request('id_rol'), function ($query, $id_rol) { 
            return $query->where('id_rol', $id_rol);
        })
        ->paginate(10)
        ->appends(request()->query());
        //$headers = ['Nombre Completo', 'Documento', 'Correo', 'Telefono', 'Trabajando en', 'Perfil', 'Estado', 'Opciones'];
        $headers = ['Nombre Completo', 'Documento', 'Correo', 'Telefono', 'Perfil', 'Estado', 'Opciones'];

        return view('user.index', compact('roles', 'title', 'items', 'headers', 'estado'));
    }

    public function create( ) 
    {
        $user = null;
        $title = 'Crear Usuarios';
        $action = 'Crear';
        $roles = User::$roles;
        $tipoDocs = User::$tipoDocumento;
        return view('user.create', compact('roles', 'title', 'action', 'tipoDocs', 'user'));
    }

    public function edit($id) 
    {
        $user = User::find($id);
        $title = 'Editar Usuarios';
        $action = 'Editar';
        $roles = User::$roles;
        $tipoDocs = User::$tipoDocumento;
        return view('user.create', compact('roles', 'title', 'action', 'tipoDocs', 'user'));
    }

    public function save(Request $req)
    {
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
            'cedula' => 'required|digits_between:6,15',
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