<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_proyecto',
        'id_user',
        'id_tarea_estado',
        'descripccion',
        'id_tarea_tipo',
        'fec_inicio',
        'fec_fin'
    ];

    protected $casts = [
        'fec_inicio' => 'datetime',
        'fec_fin' => 'datetime'
    ];

    protected $appends = ['fecIni', 'fechaFin'];

    
    public static $estado = [
        1 => 'En Pausa',
        2 => 'En Progreso',
        3 => 'Finalizado'
    ];
    public static $ClassEstado = [
        2 => 'span-yellow',
        1 => 'span-green',
        4 => 'span-red'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->id_tarea_estado] ?? 'default-class').'">'
             . (self::$estado[$this->id_tarea_estado] ?? 'Desconocido') . '</span>';
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
        $fechaActual = Carbon::now();
        $diasTranscurridos = intval($fecInicio->diffInDays($fechaActual));

        $fecFin = Carbon::parse($this->fec_fin);
        $diasTotales = intval($fecInicio->diffInDays($fecFin));

        if( $diasTotales == 0 ){
            return 100;
        }
    
        $porcentaje = intval(($diasTranscurridos / $diasTotales) * 100);
        return $porcentaje;
    }

    public function getFecIniAttribute()
    {
        $array = explode(' ', $this->fec_inicio);
        return $array[0];
    }
    public function getFechaFinAttribute()
    {
        $array = explode(' ', $this->fec_fin);
        return $array[0];
    }

    // Relaciónes 
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tareaTipo()
    {
        return $this->belongsTo(TareaTipo::class, 'id_tarea_tipo');
    }
}