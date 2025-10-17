<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaPrecio extends Model
{
    use HasFactory;

    protected $table = 'OPLN';
    protected $primaryKey = 'ListNum';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ListNum',
        'ListName',
    ];
}
