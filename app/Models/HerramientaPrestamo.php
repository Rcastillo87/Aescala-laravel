<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class HerramientaPrestamo extends Model
{
    use HasFactory;

    protected $table = 'herramienta_prestamos';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $appends = ['spanPrestamo']; 

    protected $fillable = [
        'id_herramienta',
        'id_user',
        'tipo_prestamo',
        'observacion',
        'fec_prestamo'
    ];

    // Relación con el modelo Herramienta
    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class, 'id_herramienta');
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getSpanPrestamoAttribute()
    {
        $text = '<span class="'.(self::$ClassPrestamo[$this->tipo_prestamo] ?? 'default-class').'">'
             . (self::$prestamo[$this->tipo_prestamo] ?? 'Desconocido') . '</span>';
        return $text;
    }

    public static $prestamo = [
        1 => 'Devuelto',
        2 => 'Prestado'
    ];

    public static $ClassPrestamo = [
        1 => 'span-green',
        2 => 'span-red'
    ];
}