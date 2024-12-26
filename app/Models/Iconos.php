<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iconos extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id',
    'name',
    'path',
    'created_by'
  ];

  public static function filterAndPaginatePatients($name)
  {
      $rowsPerPage = 5;

      return  Iconos::name($name)
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
