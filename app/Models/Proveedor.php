<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'inventario_proveedores';

    // Personalizar los nombres de las columnas de marca de tiempo
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'nombre_proveedor',
        'contacto',
        'telefono',
        'correo'
    ];

    public function pedidos()
    {
        return $this->hasMany(ProveedorPedido::class, 'id_proveedor');
    }
}