<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id',
    'name',
    'name_co',
    'icono_id',
    'padre',
    'orden',
    'activo',
    'created_by'
  ];

  public static function filterAndPaginatePatients($name)
  {
      $rowsPerPage = 5;

      return  Categorias::name($name)
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
}
