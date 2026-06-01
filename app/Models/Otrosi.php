<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use NumberFormatter;

class Otrosi extends Model
{
    use HasFactory;

    protected $table = 'otro_si';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_user_encargado',
        'numero',
        'fecha_creacion',
        'fecha_firma',
        'estado',
        'sugerencia_cliente',
        'img_firma'
    ];

    public static $estadoTX = [
        0 => 'Firma Pendiente',
        1 => 'Firmado',
        2 => 'Rechazado'
    ];

    public static $ClassEstado = [
        0 => 'span-blue',
        1 => 'span-green',
        2 => 'span-red'
    ];

    public static $unidades = [
        1 => 'Unid',
        2 => 'Global',
        3 => 'm2',
        4 => 'mL'
    ];

    public function getSpanEstadoAttribute()
    {
        return '<span class="'.(self::$ClassEstado[$this->estado] ?? 'default-class').'">'
             . (self::$estadoTX[$this->estado] ?? 'Desconocido') . '</span>';
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function user_encargado()
    {
        return $this->belongsTo(User::class, 'id_user_encargado', 'id');
    }

    public function area_entregable()
    {
        return $this->hasMany(AreaEntregable::class, 'id_otro_si', 'id');
    }

    public function getTokenEncripAttribute()
    {
        $token = Crypt::encryptString($this->id . '||' . $this->numero. '||' . $this->fecha_creacion);
        return rtrim(env('APP_URL'), '/') . '/firmarOtroSi/' . urlencode($token);
    }

    public function getOtroSiAttribute()
    {
        $dptArray = json_decode(
            file_get_contents(storage_path('json/jsonCityColombia.json')),
            true
        );

        $ciudad_dpt = $dptArray[$this->proyecto->departamento]['departamento'] . ', ' .
                    $dptArray[$this->proyecto->departamento]['ciudades'][$this->proyecto->ciudad];

        $valorTotal = 0;

        // Preparar adicionales
        $adicionales = $this->area_entregable;

        $arr = [];
        foreach ($adicionales as $item) {
            $area = $item->area->nombre_area;
            if (!isset($arr[$area])) {
                $arr[$area] = [
                    "espacio"       => $area,
                    "items"         => [],
                    "subtotal"      => 0
                ];
            }

            $val = $item->valor * $item->cantidad;

            $arr[$area]["items"][] = [
                "descripcion"   => $item->descripccion,
                "cantidad"      => $item->cantidad,
                "valor_unitario"=> number_format($item->valor, 0, ',', '.'),
                "valor_total"   => number_format($val, 0, ',', '.'),
                "unidad"      => $item->unidad,
            ];
            $arr[$area]["subtotal"] += $val;
            $valorTotal += $val;
        }

        $subtotal = $valorTotal;
        $txTotal = $this->numeroATexto($valorTotal);

        //$carbon = Carbon::now()->locale('es');
        $carbon = Carbon::parse($this->fecha_firma);
        $carbon->locale('es');
        $fechaOtroSi = $carbon->translatedFormat('d \d\e F \d\e Y');

        $carbon = Carbon::parse($this->fecha_creacion);
        $carbon->locale('es');
        $txFechaCrea = $carbon->translatedFormat('d \d\e F \d\e Y');

        $path = public_path('img/firmaRepre.png');
        $base64 = null;
        if (file_exists($path)) {
            $imageData = file_get_contents($path);
            $imageInfo = getimagesize($path);
            $mime = $imageInfo['mime'];
            $base64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
        }

        return [
            "id_proyecto"       => $this->id,
            "num_otro_si"       => $this->numero,
            "fecha_otro_si"     => mb_strtoupper($fechaOtroSi, 'UTF-8'),
            "nombre_cliente"    => Str::title($this->proyecto->nombre_cliente),
            "tipo_doc_cliente"  => Proyecto::$tipoDocumento[$this->proyecto->tipo_doc_cliente][1] ?? '',
            "tipo_doc_cliente_acro" => Proyecto::$tipoDocumento[$this->proyecto->tipo_doc_cliente][0] ?? '',
            "documento_cliente" => number_format($this->proyecto->cedula_cliente, 0, ',', '.'),
            "ciudad_dpt"        => $ciudad_dpt,
            "adicionales"       => $arr,
            "subtotal"          => number_format($subtotal, 0, ',', '.'),
            "valor_total"       => number_format($valorTotal, 0, ',', '.'),
            "imgRepre"          => $base64,
            "nombre_proyecto" => $this->proyecto->nombre_proyecto,
            "img_firma"         => $this->img_firma,
            "unidades"          => self::$unidades,
            "txFechaCrea"       => $txFechaCrea
        ];
    }

    public function numeroATexto($numero)
    {
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        return $formatter->format($numero);
    }

    public function getTotalDeveAttribute()
    {
        return $this->area_entregable()
                    ->selectRaw('SUM(valor * cantidad) as subtotal')
                    ->value('subtotal');
    }

    public function otrosi_refe(){
        return $this->hasMany(OtrosiRefe::class, 'id_otro_si', 'id');
    }

    public function getTotalRefeOtroSiAttribute(){
        return $this->otrosi_refe()->sum('valor');
    }

}
