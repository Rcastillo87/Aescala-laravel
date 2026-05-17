<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OtrosiRefe extends Model
{
    use HasFactory;
    protected $table = 'otro_si_refe_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_otro_si',
        'referencia',
        'valor'
    ];

    public function otro_si()
    {
        return $this->belongsTo(Otrosi::class, 'id_otro_si', 'id');
    }

}
