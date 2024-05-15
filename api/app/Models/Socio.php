<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'socios';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'sede',
        'price',
        'last_pay',
        'expiration',
        'activo'
    ];

    protected $attributes = [
        'dni' => '',
        'nombre' => '',
        'apellido' => '',
        'telefono' => '',
        'email' => '',
        'sede' => '',
        'price' => '',
        'last_pay' => '',
        'expiration' => '',
        'activo' => ''
    ];
    public $timestamps = false;
}
