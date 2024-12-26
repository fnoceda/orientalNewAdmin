<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ventas_detalles extends Model
{
    protected $table = 'ventas_detalles';
    protected $fillable = [
        'venta_id'
        ,'articulo_id'	
        ,'cantidad'	
        ,'precio'	
        ,'total'		
        ,'sabor'		
        ,'color'		
        ,'medida'
        ,'created_at'		
        ,'updated_at'		
        ,'deleted_at'			
        ,'created_by'	
        ,'updated_by'	
        ,'deleted_by'
    ];

   
    public function articulo()
    {
        return $this->belongsTo(Articulos::class);
    }
  
}
