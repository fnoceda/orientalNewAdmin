<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulos extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id',
    'name',
    'descripcion',
    'name_co',
    'descripcion_name',
    'valoracion',
    'codigo',
    'barra',
    'observaciones',
    'observaciones_co',
    'activo',
    'costo_actual',
    'costo_promedio',
    'precio_venta',
    'existencia',
    'ultima_compra',
    'ultima_venta',
    'es_combo',
    'categoria_id',
    'created_by'
  ];

  public static function filterAndPaginatePatients($name)
  {
      $rowsPerPage = 5;

      return  Articulos::name($name)
          ->orderBy('id')
  //            ->paginate($rowsPerPage);
          ->get();
  }

  public function scopeName($query, $name)
  {
      if($name){
          return $query->where('id', 'ilike', '%'.$name.'%');
      }
  }
  public function articuloimagen()
    {
        return $this->hasMany(ArticuloImagenes::class,'articulo_id');
    }
    public function ventaDetalles()
    {
        return $this->hasMany(Ventas_detalles::class);
    }
}
