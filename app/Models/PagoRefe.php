<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class PagoRefe extends Model
{
    use HasFactory;
    protected $table = 'pago_referencia';
    public $timestamps = false;

    protected $fillable = [
        'id_pago',
        'reference_type',
        'reference_id',
        'concepto',
        'valor'
    ];

    /**
     * Relación polimórfica: permite que el documento pertenezca a Pagos, Usuarios, etc.
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

}
