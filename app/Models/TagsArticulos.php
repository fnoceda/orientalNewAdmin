<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TagsArticulos extends Model
{
    protected $table = 'tags_articulos';
    protected $fillable = [
        'tag_id',
        'articulo_id',
      ];
      public static function boot()
      {
         parent::boot();
         static::creating(function($model)
         {
             $user = Auth::user();
             if(!empty($user->id)){
              $model->created_by = $user->id;
              $model->updated_at = null;
              $model->deleted_by = null;
             }
  
         });
         static::updating(function($model)
         {
             $user = Auth::user();
             if(!empty($user->id)){
              $model->updated_by = $user->id;
              $model->updated_at = Carbon::now();
             }
  
         });
      }
}
