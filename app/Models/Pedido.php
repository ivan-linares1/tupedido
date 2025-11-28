<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'ORDR';
    protected $primaryKey = 'DocEntry';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'DocNum',
        'CardCode', 
        'DocDate', 
        'DocDueDate', 
        'CardName', 
        'SlpCode',
        'Phone1', 
        'E_Mail', 
        'DocCur', 
        'ShipToCode', 
        'PayToCode',
        'Address', 
        'Address2',
        'TotalSinPromo', 
        'Descuento', 
        'Subtotal',
        'IVA', 
        'Total',
        'comment',
        'DocStatus',
        'Status',
    ];

    // Relación con las líneas de cotización
    public function lineas()
    {
        return $this->hasMany(LineasPedidos::class, 'DocEntry', 'DocEntry');
    }

    // Relación con el vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedores::class, 'SlpCode', 'SlpCode');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'DocCur', 'Currency_ID');
    }

    // Relación con la cotización base (OQUT)
    public function cotizacionBase()
    {
        return $this->belongsTo(Cotizacion::class, 'BaseEntry', 'DocEntry');
    }

    
}
