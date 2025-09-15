<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDescuento extends Model
{
    protected $table = 'EDG1'; // nombre real en BD
    public $incrementing = false;
    public $timestamps = false;

    // Laravel no maneja bien PK compuestas, así que definimos sin $primaryKey
    protected $fillable = [
        'AbsEntry',
        'ObjType',
        'ObjKey',
        'Disctype',
        'Discount',
    ];

    // Un detalle pertenece a un descuento
    public function descuento()
    {
        return $this->belongsTo(Descuento::class, 'AbsEntry', 'AbsEntry');
    }

    // Un detalle pertenece a un grupo de artículos
    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'ObjKey', 'ItmsGrpCod');
    }
}
