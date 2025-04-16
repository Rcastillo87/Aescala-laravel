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

    public function getDiasTranscurridosAttribute ()
    {
        $fecInicio = Carbon::parse($this->fec_inicio);
        $fechaActual = Carbon::now();
        return intval($fecInicio->diffInDays($fechaActual));
    }

    public function getDiasProcentageAttribute()
    {
        $fecInicio = Carbon::parse($this->fec_inicio);
        $fecFinEstimado = Carbon::parse($this->fec_fin_estimado);
        
        $diasTranscurridos = $this->dias_transcurridos;
        $diasEstimados = $fecInicio->diffInDays($fecFinEstimado);
    
        if ($diasEstimados == 0) {
            return 100;
        }
    
        $porcentaje = intval(($diasTranscurridos / $diasEstimados) * 100);
        //min($porcentaje, 100);
        return $porcentaje;
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

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_proyecto', 'id');
    }
}