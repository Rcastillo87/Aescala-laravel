<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

use App\Models\User;

class DespachoController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Usuarios';
        $roles = User::$roles;
        $estado = User::$estado;
        $items = User::when(Request('nombre_completo'), function ($query, $nombre_completo) { 
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
        ->paginate(10);
        $headers = ['Nombre Completo', 'Documento', 'Correo', 'Telefono', 'Fecha de Creación', 'Perfil', 'Estado', 'Opciones'];
        return view('user.index', compact('roles', 'title', 'items', 'headers', 'estado'));
    }
}