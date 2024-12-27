<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class PrivilegiosController extends Controller
{
 
     public function index(){
         $foo['perfiles'] = self::getPerfiles();
         return view('auth.perfiles')->with(['foo'=>$foo]);
 
     }
 
     public function getPerfil(Request $r){
         DB::enableQueryLog(); 
 
         $sql = ' select id, name from menus where padre is null order by orden ';
         $menus =  DB::select($sql); 
         Log::info(DB::getQueryLog());
 
         $sql = ' select id, name, padre from menus where padre is not null order by padre, orden ';
         $sub_menus =  DB::select($sql); 
         Log::info(DB::getQueryLog());
         
         Log::info(DB::getQueryLog());
 
         $sql = " select 
                     'op_'||mpa.perfil||'_'||mpa.menu as opcion
                     from menus_perfiles mpa 
                     where mpa.deleted_by is null 
                     and mpa.perfil = ".$r->perfil;
         $permisos = DB::select($sql); 
         Log::info(DB::getQueryLog());
 
         $foo['perfil'] = $r->perfil;
         $foo['menus'] = $menus;
         $foo['sub_menus'] = $sub_menus;
         $foo['permisos'] = $permisos; 
 
         return view('auth.privilegios')->with(['foo'=>$foo]);
 
     }
 
     public function addPrivilegio(Request $request){
         DB::enableQueryLog();   
 
         $datos = explode('_', $request->opcion);
         $where['perfil'] = $datos[1]; 
         $where['menu']   = $datos[2]; 
         //1ro actualizamos si $updates > 0  retornamos, sino
         $campos['deleted_by'] = NULL;
         $campos['deleted_at'] = NULL;
  
         $updates = DB::table('menus_perfiles')
         ->where($where)
         ->update($campos);
         Log::info(DB::getQueryLog());
         
 
         if($updates > 0){ //actualizamos por si ya existe y esta como eliminado por softDeletes
             return $updates;
         }else{ //quiere decir que no existe luego en la tabla asi que hay que insertar
 
             $insert = DB::table('menus_perfiles')->insert(
                 Array(  'perfil' => $datos[1], 
                         'menu' => $datos[2], 
                         'created_by' => auth()->user()->id, 
                         'created_at' => 'now()')
             );
             Log::info(DB::getQueryLog());
             if($insert == true){
                 return 1;
             }else{
                 return 0;
             }
         }
     }
     public function delPrivilegio(Request $request){
         DB::enableQueryLog();   
         $datos = explode('_', $request->opcion);
         $where['perfil'] = $datos[1]; 
         $where['menu']   = $datos[2]; 
         $campos['deleted_by'] = auth()->user()->id;
         $campos['deleted_at'] = 'now()';
 
         $updates = DB::table('menus_perfiles')
         ->where($where)
         ->update($campos);
         Log::info(DB::getQueryLog());
 
         return $updates;
     }
 
     private function getPerfiles(){
         $sql = ' select id, name, comentario from perfiles where deleted_by is null ';
         return DB::select($sql); 
     }

}
