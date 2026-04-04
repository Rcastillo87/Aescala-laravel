<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoEntregado extends Model
{
    use HasFactory;

    protected $table = 'insumos_entregados';

    protected $fillable = [
        'id_area_empresa',
        'id_user',
        'id_insumo',
        'cantidad'
    ];

    // Relación con el modelo insumo
    public function insumo()
    {
        return $this->belongsTo(Insumos::class, 'id_insumo');
    }

    // Relación con el modelo area empresa
    public function area_empresa()
    {
        return $this->belongsTo(AreasEmpresa::class, 'id_area_empresa');
    }

    // Relación con el modelo usuarios
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}
