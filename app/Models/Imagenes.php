<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagenes extends Model
{
    protected $table = 'Imagenes'; // Nombre real de la tabla
    protected $primaryKey = 'Id_imagen';
    public $timestamps = false;

    protected $fillable = [
        'Ruta_imagen',
    ];

     // Relación: una imagen puede estar asociada a muchos artículos
    public function articulos()
    {
        return $this->hasMany(Articulo::class, 'Id_imagen', 'Id_imagen');
    }
}
