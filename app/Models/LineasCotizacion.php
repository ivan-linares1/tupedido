<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineasCotizacion extends Model
{
    use HasFactory;

    protected $table = 'QUT1';
     protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'DocEntry',
        'LineNum', 
        'BaseLine',
        'ItemCode',
        'U_Dscr', 
        'unitMsr2',
        'Price', 
        'DiscPrcnt', 
        'Quantity', 
        'Id_imagen',
        'ivaPorcentaje',
        'Subtotal',
        'Descuento',
        'Total'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'DocEntry', 'DocEntry');
    }

    public function imagen()
    {
        return $this->belongsTo(Imagenes::class, 'Id_imagen', 'Id_imagen');
    }

    public function impuesto() {
        return$this->belongsTo(impuestos::class, 'ivaPorcentaje', 'Code');
    }
}
