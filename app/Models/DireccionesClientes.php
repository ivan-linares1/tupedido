<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DireccionesClientes extends Model
{
    protected $table = 'CRD1'; // Nombre real de la tabla
    public $incrementing = false; // porque no es INT autoincremental
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CardCode',
        'Address',
        'Street',
        'Block',
        'ZipCode',
        'City',
        'Country',
        'County',
        'State',
        'AdresType'
    ];

    // Relación: una dirección pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'CardCode', 'CardCode');
    }

}
