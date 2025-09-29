<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    protected $table = 'OITB';
    protected $primaryKey = 'ItmsGrpCod';
    public $incrementing = false; 
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ItmsGrpCod',
        'ItmsGrpNam',
        'Locked',
        'Object',
    ];

    // Relación una marca tiene muchos artículos
    public function articulos()
    {
        return $this->hasMany(Articulo::class, 'ItmsGrpCod', 'ItmsGrpCod');
    }
}
