<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nombre',
        'mime_type',
        'contenido',
    ];

    protected $hidden = [
        'contenido', // Esto evita que el binario se incluya en respuestas JSON o arrays
    ];

    /**
     * Relación polimórfica: permite que el documento pertenezca a Pagos, Usuarios, etc.
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * ACCESSOR: Descomprime el contenido automáticamente al acceder a él.
     * Uso: $documento->contenido_descomprimido
     */
    public function getContenidoDescomprimidoAttribute()
    {
        return $this->contenido ? gzdecode($this->contenido) : null;
    }

    /**
     * SCOPE: Útil para buscar documentos de un tipo específico si no usas la relación.
     */
    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('documentable_type', $tipo);
    }
}
