<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedores extends Model
{
    
    protected $table = 'OSLP'; // Nombre de la tabla
    protected $primaryKey = 'SlpCode'; // Clave primaria
    public $incrementing = false; // Tipo de clave primaria (si no es auto-incremental)
    protected $keyType = 'int'; // Tipo de clave primaria
    public $timestamps = false; // Desactivar timestamps si no existen columnas created_at/updated_at

    protected $fillable = [
        'SlpCode',
        'SlpName',
        'Active'
    ];
}
