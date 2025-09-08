<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'OCRD'; // Nombre real de la tabla
    protected $primaryKey = 'CardCode';
    public $incrementing = false; // porque no es INT autoincremental
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CardCode',
        'CardName',
        'GroupNum',
    ];

    // Relación: un cliente tiene muchas direcciones
    public function direcciones()
    {
        return $this->hasMany(DireccionesClientes::class, 'CardCode', 'CardCode');
    }
}
