<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avance extends Model
{
    use HasFactory;

    protected $table = 'tarea_avances';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id_tarea',
        'avance',
        'fec_avance'
    ];

    // Relaciónes 
    public function avance()
    {
        return $this->belongsTo(Tarea::class, 'id_tarea');
    }
}