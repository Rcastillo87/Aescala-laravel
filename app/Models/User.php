<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

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

    public function getIsAdminAttribute()
    {
        return ($this->id_rol == 1)? true: false;
    }

    public function getIsUserAttribute()
    {
        return ($this->id_rol == 2)? true: false;
    }

    public function getIsColabAttribute()
    {
        return ($this->id_rol == 3)? true: false;
    }

    public function getIsComerAttribute()
    {
        return ($this->id_rol == 4)? true: false;
    }

    public function getIscarteraAttribute()
    {
        return ($this->id_rol == 5)? true: false;
    }

    public function getIsAnalistaAttribute()
    {
        return ($this->id_rol == 6)? true: false;
    }

    public function getIsContratistaAttribute()
    {
        return ($this->id_rol == 7)? true: false;
    }

    public function getNewProyectAttribute()
    {
        return Proyecto::where('id_estado', 2)->count();
    }

    public function getnewSolicitudAttribute()
    {
        return SolicitudMaterial::when(Auth::user()->isColab, function ($query) {
                $query->where('id_user', Auth::user()->id)->where('estado', 1);
            })
            ->when(Auth::user()->isAnalista, function ($query) {
                return $query->whereHas('items', function ($q) {
                    $q->where('aprobado', 0);
                });
            })
            ->when(Auth::user()->isAdmin, function ($query) {
                $query->where('estado', 1);
            })
            ->count();

    }

    public static function getnewCarteraProAttribute()
    {
        return Proyecto::whereIn('id_estado', [1, 5])
            ->whereHas('entreProyecto')
            ->whereDoesntHave('pagos', function ($q) {
                $q->where('tipo_pago', 1);
            })
            ->count();
    }

    public static function getnewCarteraOtroSiAttribute()
    {
        return Otrosi::whereIn('estado', [1])
            ->whereHas('proyecto', function ($q) {
                $q->whereIn('id_estado', [1, 5]);
            })
            ->whereDoesntHave('pagos', function ($q) {
                $q->where('tipo_pago', 2);
            })
            ->count();
    }

    public static $roles = [
        1 => 'Administrador',
        2 => 'Usuario',
        3 => 'Arquitecto',
        4 => 'Comercial',
        5 => 'Cartera',
        6 => 'Analista',
        7 => 'Contratista',
        8 => 'Tecnico'
    ];

    public static $ClassRol = [
        1 => 'span-blue',
        2 => 'span-yellow',
        3 => 'span-gray',
        4 => 'span-red',
        5 => 'span-green',
        6 => 'span-black',
        7 => 'span-cyan',
        8 => 'span-orange'
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
        1 => ['CC', 'Cedula De Ciudadania'],
        2 => ['CE', 'Cedula De Extrangeria'],
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
