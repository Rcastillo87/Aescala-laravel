<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiasNoLaboralos extends Model
{
    use HasFactory;

    protected $table = 'dias_no_laboradosxproy';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'dia',
        'detalle'
    ];

    // Relación con el modelo proyecto
    public function proyecto()
    {
        return $this->hasMany(Proyecto::class, 'id_proyecto');
    }
}
