<?php

namespace App\Models;

use App\View\Components\AppLayout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use NumberFormatter;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';
    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_proyecto',
        'departamento',
        'ciudad',
        'direccion',
        'ubicacion',
        'cedula_cliente',
        'tipo_doc_cliente',
        'nombre_cliente',
        'telefono_cliente',
        'dias_contrato',
        'dias_trabajo',
        'id_estado',
        'id_user',
        'id_user_obra_blanca',
        'id_user_carpinteria',
        'id_user_comercial',
        'area_privada',
        'termino_1_por',
        'termino_2_por',
        'termino_3_por',
        'termino_4_por',
        'termino_5_por',
        'termino_6_por',
        'opcion',
        'por_inicia',
        'img_firma',
        'fec_inicio',
        'fec_fin_estimado',
        'fec_fin_real',
        'observacion',
        'descuento',
        'paz_salvo',
        'acepta_trata_datos',
        'fecha_firma',
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
        5 => 'Posventas',
        6 => 'Borrar Firma',
        7 => 'Diseño'
    ];

    public static $ubicacion = [
        0 => 'Norte',
        1 => 'Sur',
        2 => 'Este (Oriente)',
        3 => 'Oeste (Occidente)',
        4 => 'Noreste (Entre el Norte y el Este)',
        5 => 'Noroeste (Entre el Norte y el Oeste)',
        6 => 'Sureste (Entre el Sur y el Este)',
        7 => 'Suroeste (Entre el Sur y el Oeste)',
        8 => 'Centro'
    ];

    public static $estado0 = [
        1 => 'Pendiente Firma',
        2 => 'Separación de Cupo',
    ];

    public static $ClassEstado = [
        1 => 'span-green',
        2 => 'span-yellow',
        3 => 'span-blue',
        4 => 'span-red',
        5 => 'span-black',
        6 => 'span-purple',
        7 => 'span-orange'
    ];

    public static $ClassEstado0 = [
        1 => 'span-blue',
        2 => 'span-red',
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

    public function getSpanEstado0Attribute()
    {
        if(!$this->img_firma){
            return '<span class="'.(self::$ClassEstado0[1]).'">'
                 . (self::$estado0[1] ?? 'Desconocido') . '</span>';
        }
        return '<span class="'.(self::$ClassEstado0[2] ?? 'default-class').'">'
             . (self::$estado0[2]) . '</span>';
    }

    public function getSpanTratadatosAttribute()
    {
        return ($this->acepta_trata_datos==1) ? '<span class="span-green">SI</span>':
            '<span class="span-red">NO</span>';
    }

    public function getContratoAttribute()
    {
        $dptArray = json_decode(
            file_get_contents(storage_path('json/jsonCityColombia.json')),
            true
        );

        $ciudad_dpt = $dptArray[$this->departamento]['departamento'] . ', ' .
                    $dptArray[$this->departamento]['ciudades'][$this->ciudad];

        $meses   = ceil($this->dias_trabajo / 24);
        $txMeses = $this->numeroATexto($meses);
        $total   = $this->entreProyecto()
                    ->selectRaw('SUM(valor_total * cantidad) as total')
                    ->value('total');
        $total = $total - $this->descuento;
        $txTotal = $this->numeroATexto($total);

        $valTerm1 = ceil($total * $this->termino_1_por / 100);
        $valTerm2 = ceil($total * $this->termino_2_por / 100);
        $valTerm3 = ceil($total * $this->termino_3_por / 100);
        $valTerm4 = ceil($total * $this->termino_4_por / 100);
        $valTerm5 = ceil($total * $this->termino_5_por / 100);
        $valTerm6 = ceil($total * $this->termino_6_por / 100);

        $carbon = Carbon::parse($this->fecha_firma);
        $carbon->locale('es');
        $fechaTexto = $this->fecha_firma? $carbon->translatedFormat('d \d\e F \d\e Y') : null;

        // Preparar entregables para la vista
        $entregables = $this->entreProyecto->map(function($e){
            return [
                "cantidad" => $e->cantidad,
                "titulo" => $e->entregable->nombre_estregable,
                "items"  => explode("||", $e->tx_entregable),
                "precio" => number_format($e->valor_total * $e->cantidad, 0, ',', '.')
            ];
        })->toArray();
        if($this->descuento > 0){
            $entregables[] = [
                "cantidad" => 1,
                "titulo" => 'Descuento',
                "items"  => ['Descuento Aceptado por Gerencia'],
                "precio" => '-'.number_format($this->descuento, 0, ',', '.')
            ];
        }

        $path = public_path('img/firmaRepre.png');
        if (file_exists($path)) {
            $imageData = file_get_contents($path);
            $imageInfo = getimagesize($path);
            $mime = $imageInfo['mime'];
            $base64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
        } else {
            $base64 = null;
        }

        // Preparar datos para la vista
        return [
            "id_proyecto"         => $this->id,
            "fecha_contrato"      => mb_strtoupper($fechaTexto?? '', 'UTF-8'),
            "nombre_cliente"      => Str::title($this->nombre_cliente),
            "ciudad_dpt"          => $ciudad_dpt,
            "tipo_doc_cliente"    => self::$tipoDocumento[$this->tipo_doc_cliente][1] ?? '',
            "tipo_doc_cliente_acro" => self::$tipoDocumento[$this->tipo_doc_cliente][0] ?? '',
            "documento_cliente"   => number_format($this->cedula_cliente, 0, ',', '.'),
            "direccion_proye"     => $this->direccion,
            "area_privada_proye"  => $this->area_privada,
            "dias_proye"          => $this->dias_contrato,
            "meses_proye"         => $meses,
            "tx_meses_proye"      => Str::title($txMeses),
            "tx_valor_total"      => Str::title($txTotal),
            "valor_total"         => number_format($total, 0, ',', '.'),
            "val_term_1"          => number_format($valTerm1, 0, ',', '.'),
            "val_term_2"          => number_format($valTerm2, 0, ',', '.'),
            "val_term_3"          => number_format($valTerm3, 0, ',', '.'),
            "val_term_4"          => number_format($valTerm4, 0, ',', '.'),
            "val_term_5"          => number_format($valTerm5, 0, ',', '.'),
            "val_term_6"          => number_format($valTerm6, 0, ',', '.'),
            "por_term_1"          => $this->termino_1_por,
            "por_term_2"          => $this->termino_2_por,
            "por_term_3"          => $this->termino_3_por,
            "por_term_4"          => $this->termino_4_por,
            "por_term_5"          => $this->termino_5_por,
            "por_term_6"          => $this->termino_6_por,
            "img_firma"           => $this->img_firma,
            "entregables"         => $entregables,
            "dias_trabajo"        => $this->dias_trabajo,
            'imgRepre'            => $base64,
            "descuento"           => $this->descuento ?? 0,
            "acepta_tratamiento_datos" => ($this->acepta_trata_datos == 1) ? true : false,
            "notas"               => $this->nota_proyecto
        ];
    }

    public function getTrataDatosAttribute()
    {
        $carbon = Carbon::parse($this->fecha_firma);
        $carbon->locale('es');
        $fechaTexto = $this->fecha_firma? $carbon->translatedFormat('d \d\e F \d\e Y') : null;

        // Preparar datos para la vista
        return [
            "nombre_cliente"      => Str::title($this->nombre_cliente),
            "tipo_doc_cliente"    => self::$tipoDocumento[$this->tipo_doc_cliente][1] ?? '',
            "tipo_doc_cliente_acro" => self::$tipoDocumento[$this->tipo_doc_cliente][0] ?? '',
            "documento_cliente"   => number_format($this->cedula_cliente, 0, ',', '.'),
            "img_firma"           => $this->img_firma,
            "fechaTexto"         => $fechaTexto,
            "fecha_contrato"     => Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y'),
        ];
    }

    public function numeroATexto($numero)
    {
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        return $formatter->format($numero);
    }

    public function diasHabilesTrascurridos($hoy, $diasFestivos)
    {
        $festivos = new Festivos();
        return $festivos->contarDiasHabiles($this->fec_inicio, $hoy, $diasFestivos, $this->id);
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

    public function getApazAttribute()
    {
        return ($this->paz_salvo==1) ? '<span class="span-green">SI</span>':
            '<span class="span-red">NO</span>';
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

    public function getTokenEncripAttribute()
    {
        $token = Crypt::encryptString($this->id . '||' . $this->cedula_cliente);
        return rtrim(config('app.url'), '/')
            . '/firmarContrato/'
            . urlencode($token);
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

    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'id_proyecto', 'id');
    }

    public function otro_si()
    {
        return $this->hasMany(Otrosi::class, 'id_proyecto', 'id');
    }

    public function nota_proyecto()
    {
        return $this->hasMany(NotasProyecto::class, 'id_proyecto', 'id');
    }

    public function getTotalAttribute()
    {
        return $this->entreProyecto()
            ->selectRaw('SUM(valor_total * cantidad) as total')
            ->value('total') - $this->descuento;
    }

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->where(['tipo_pago' => 1])->sum('valor_pagado');
    }

    public function getPagosAttribute()
    {
        $arrayPagos = [];
        $total = $this->total;
        $txtArr = [
            1 => 'correspondiente al inicio de la etapa de diseño.',
            2 => 'correspondiente a la aprobación del diseño para dar inicio a la obra.',
            3 => 'correspondiente al corte de carpintería.',
            4 => 'correspondiente al inicio de la instalación de carpintería.',
            5 => 'correspondiente al inicio de la instalación de accesorios, grifería y mesón.',
            6 => 'correspondiente al pago final por la entrega de la obra.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $campo = "termino_{$i}_por";
            if (!empty($this->$campo) && $this->$campo > 0) {
                $porcentaje = $this->$campo;
                $arrayPagos[] = [
                    'msg' => "Pago del {$porcentaje}% - {$txtArr[$i]}",
                    'termino' => $i,
                    'porcentaje' => $porcentaje,
                    'valor_apagar' => ceil($total * $porcentaje / 100),
                ];
            }
        }
        return $arrayPagos;
    }

}
