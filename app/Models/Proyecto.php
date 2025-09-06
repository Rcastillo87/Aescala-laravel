<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_proyecto',
        'departamento',
        'ciudad',
        'direccion',
        'cedula_cliente',
        'tipo_doc_cliente',
        'nombre_cliente',
        'telefono_cliente',
        'dias_trabajo',
        'id_estado',
        'id_user',
        'id_user_obra_blanca',
        'id_user_carpinteria',
        'id_user_comercial',
        'area_privada',
        'aprov_diseno_por',
        'ini_carpinteria_por',
        'ini_enchape_por',
        'ini_griferia_por',
        'entrega_obra_por',
        'opcion',
        'por_inicia',
        //'fec_begin_cont'
    ];

    protected $casts = [
        'fec_inicio' => 'datetime',
        'fec_fin_estimado' => 'datetime',
        'fec_fin_real' => 'datetime'
    ];

    public static $estado = [
        1 => 'En Desarrollo',
        2 => 'Nuevo',
        3 => 'Entregado',
        4 => 'Cancelado',
        5 => 'Posventas'
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-red',
        5 => 'span-black'
    ];

    public static $tipoDocumento = [
        1 => ['CC', 'Cedula De Ciudadania'],
        2 => ['CE', 'Cedula De Extrangeria'],
        3 => ['PAS', 'Pasaporte'],
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->id_estado] ?? 'default-class').'">'
             . (self::$estado[$this->id_estado] ?? 'Desconocido') . '</span>';
    }


    public function diasHabilesTrascurridos($hoy, $diasFestivos)
    {
        $festivos = new Festivos();
        return $festivos->contarDiasHabiles($this->fec_inicio, $hoy, $diasFestivos);
    }

    public function getFecIniAttribute()
    {
        $array = explode(' ', $this->fec_inicio);
        return $array[0];
    }
    public function getFecfinEstAttribute()
    {
        $array = explode(' ', $this->fec_fin_estimado);
        return $array[0];
    }

    public function getTotalFinanzasAttribute()
    {
        $totals = $this->finanzas()
            ->selectRaw("
                SUM(CASE WHEN tipo = 1 THEN valor ELSE 0 END) as ingresos,
                SUM(CASE WHEN tipo = 2 THEN valor ELSE 0 END) as gastos
            ")
            ->first();
        
        return ($totals->ingresos ?? 0) - ($totals->gastos ?? 0);
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function userOB()
    {
        return $this->belongsTo(User::class, 'id_user_obra_blanca');
    }

    public function userCarpi()
    {
        return $this->belongsTo(User::class, 'id_user_carpinteria');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_proyecto', 'id');
    }

    public function finanzas()
    {
        return $this->hasMany(Finanza::class, 'id_proyecto', 'id');
    }

    public function despachos()
    {
        return $this->hasMany(Despachos::class, 'id_proyecto', 'id');
    }

    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class, 'id_proyecto', 'id');
    }

    public function entreProyecto()
    {
        return $this->hasMany(EntregableProye::class, 'id_proyecto', 'id');
    }
}