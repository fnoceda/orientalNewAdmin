<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $fillable = [
        'id',
        'name',
        'porcentaje_descuento',
        'path',
        'created_by',
      ];
}
