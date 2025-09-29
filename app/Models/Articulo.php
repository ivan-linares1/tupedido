<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'OITM'; // Nombre real de la tabla
    protected $primaryKey = 'ItemCode';
    public $incrementing = false; // porque no es INT autoincremental
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ItemCode',
        'ItemName',
        'FrgnName',
        'SalUnitMsr',
        'Id_imagen',
        'Active',
        'ItmsGrpCod',
    ];

     // Relación: un artículo pertenece a una imagen (imagenes)
    public function imagen()
    {
        return $this->belongsTo(Imagenes::class, 'Id_imagen', 'Id_imagen');
    }

    // Relación: un artículo tiene muchos precios (itm1)
    public function precio()
    {
        return $this->hasOne(Precios::class, 'ItemCode', 'ItemCode');
    }

     // Relación: un artículo pertenece a un grupo de artículos (oitb)
    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'ItmsGrpCod', 'ItmsGrpCod');
    }
}
