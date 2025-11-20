<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre de la tabla
    protected $table = 'users';

    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'id',
        'nombre',
        'email',
        'password',
        'activo',
        'rol_id',
        'codigo_cliente',
        'codigo_vendedor',
        'max_sessions',
    ];

    /**
     * Atributos que deben ocultarse al serializar.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Tipos de conversión de atributos.
     */
    protected $casts = [
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    /**
     * relacion de usuario con rol
     */
    public function rol(){
         return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
}
