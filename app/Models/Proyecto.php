<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';
    
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_proyecto',
        'departamento',
        'ciudad',
        'direccion',
        'nombre_cliente',
        'telefono_cliente',
        'val_obra_blanca',
        'val_obra_blanca_materiales',
        'val_obra_carpinteria',
        'val_carpinteria_materiales',
        'pres_otros',
        'observacion',
        'fec_inicio',
        'fec_fin_estimado',
        'dias_trabajo',
        'fec_fin_real',
        'id_estado',
        'id_user'
    ];

    protected $casts = [
        'fec_inicio' => 'datetime',
        'fec_fin_estimado' => 'datetime',
        'fec_fin_real' => 'datetime'
    ];

    public static $estado = [
        1 => 'En Desarrollo',
        2 => 'Cotizado',
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

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->id_estado] ?? 'default-class').'">'
             . (self::$estado[$this->id_estado] ?? 'Desconocido') . '</span>';
    }

    public function getTotalProyectoAttribute ()
    {
        return $this->val_obra_blanca + $this->val_obra_blanca_materiales + $this->val_obra_carpinteria + $this->val_carpinteria_materiales + $this->pres_otros;
    }

    public function getTotalManoAttribute ()
    {
        return $this->val_obra_blanca + $this->val_obra_carpinteria + $this->pres_otros;
    }

    public function getTotalMaterialesAttribute ()
    {
        return $this->val_obra_blanca_materiales + $this->val_carpinteria_materiales;
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
}