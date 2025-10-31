<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class impuestos extends Model
{
    use HasFactory;

    protected $table = 'OSTC'; // nombre exacto de la tabla
    protected $primaryKey = 'Code'; // clave primaria personalizada
    public $incrementing = false; // porque Code no es autoincremental
    protected $keyType = 'string'; // tipo de la clave

    protected $fillable = [
        'Code',
        'Name',
        'Rate',
        'Lock',
    ];

    public $timestamps = false;
}
