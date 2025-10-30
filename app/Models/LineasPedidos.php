<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineasPedidos extends Model
{
    use HasFactory;

    protected $table = 'RDR1';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

     protected $fillable = [
        'DocEntry',
        'LineNum',
        'ObjType',
        'ItemCode',
        'U_Dscr',
        'unitMsr2',
        'Price',
        'DiscPrcnt',
        'Quantity',
        'Id_imagen',
        'TargetType',
        'TrgetEntry',
        'BaseRef',
        'BaseType',
        'BaseEntry',
        'ivaPorcentaje'
    ];


    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'DocEntry', 'DocEntry');
    }

    public function imagen()
    {
        return $this->belongsTo(Imagenes::class, 'Id_imagen', 'Id_imagen');
    }
}
