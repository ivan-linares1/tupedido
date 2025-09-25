<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pedidos extends Model
{
    protected $table = 'RDR1';

    // Si no tienes created_at / updated_at en la tabla
    public $timestamps = false;

    protected $primaryKey = 'DocEntry';
    public $incrementing = false; // Si DocEntry no es auto_increment en todas las filas

    protected $fillable = [
        'DocEntry',
        'fecha',
        'LineNum',
        'TargetType',
        'TrgetEntry',
        'BaseRef',
        'BaseType',
        'BaseEntry',
    ];

    // Relación con la cotización base (OQUT)
    public function cotizacionBase()
    {
        return $this->belongsTo(Cotizacion::class, 'BaseEntry', 'DocEntry');
    }

}
