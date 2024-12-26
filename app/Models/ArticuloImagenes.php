<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloImagenes extends Model
{
    
    protected $table = 'articulo_imagenes';
    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
    protected $fillable = [
        'articulo_id'	
        ,'path'	
        ,'orden'	
        ,'medida'
        ,'created_at'		
        ,'updated_at'		
        ,'deleted_at'			
        ,'created_by'	
        ,'updated_by'	
        ,'deleted_by'
    ];

}
