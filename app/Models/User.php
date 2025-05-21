<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre_completo',
        'email',
        'password',
        'cedula',
        'tipo_documento',
        'telefono',
        'direccion',
        'id_rol',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->activo] ?? 'default-class').'">'
             . (self::$estado[$this->activo] ?? 'Desconocido') . '</span>';
    }

    public function getSpanRolAttribute()
    {
        return '<span class="'.(self::$ClassRol[$this->id_rol] ?? 'default-class').'">'
             . (self::$roles[$this->id_rol] ?? 'Desconocido') . '</span>';
    }

    public function getClassRolAttribute()
    {
        return self::$ClassRol[$this->id_rol] ?? 'span-black';
    }
    public function getNameRolAttribute()
    {
        return self::$roles[$this->id_rol] ?? 'Desconocido';
    }

    public function getClassEstadoAttribute()
    {
        return self::$ClassEstado[$this->activo] ?? 'span-black';
    }
    public function getEstadoAttribute()
    {
        return self::$estado[$this->activo] ?? 'Desconocido';
    }
    public function getTipoDocAttribute()
    {
        return self::$tipoDocumento[$this->tipo_documento] ?? 'Desconocido';
    }

    public function getUserIsValidAttribute()
    {
        if (($this->activo == 2) || ($this->id_rol == 3)) {
            return false;
        }
        return true;
    }

    public function getProgresProyectoAttribute()
    {
        return $this->proyectos->whereIn('id_estado', [1, 5]);
    }

    public function getProgresTareaAttribute()
    {
        return $this->tareas->whereIn('id_tarea_estado', [2]);
    }

    public function getIsNotColabAttribute()
    {
        return ($this->id_rol != 3)? true: false;
    }

    public static $roles = [
        1 => 'Administrador',
        2 => 'Usuario',
        3 => 'Colaborador'
    ];

    public static $ClassRol = [
        1 => 'span-blue',
        2 => 'span-yellow',
        3 => 'span-gray'
    ];

    public static $estado = [
        1 => 'Activo',
        2 => 'Desactivado'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-red'
    ];

    public static $tipoDocumento = [
        1 => ['CC', 'Cedu. Ciudadania'],
        2 => ['CE', 'Cedu. Extrangeria'],
        3 => ['PAS', 'Pasaporte'],
    ];

    //RELACIONES 
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_user');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_user');
    }

}
