<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'OEDG'; // nombre real en BD
    protected $primaryKey = 'AbsEntry';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'AbsEntry',
        'Type',
        'ObjType',
        'ObjCode',
    ];

    // Un descuento pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'ObjCode', 'CardCode');
    }

    // Un descuento tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(DetalleDescuento::class, 'AbsEntry', 'AbsEntry');
    }
}
