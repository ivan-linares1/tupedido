<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];

    /**
     * Relación con los usuarios.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id');
    }
}
