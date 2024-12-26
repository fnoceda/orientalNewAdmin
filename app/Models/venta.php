<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class venta extends Model
{
    protected $fillable = [
        'fecha' ,
        'canal',
        'modo' ,
        'forma_pago' ,
        'ciudad_id' ,
        'direccion' ,
        'latitud',
        'longitud' ,
        'estado',
        'cliente_id' ,
        'created_by'
      ];
}
