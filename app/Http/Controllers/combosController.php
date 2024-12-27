<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class combosController extends Controller
{
    public function index(){
        Log::info(__FILE__.'/'.__FUNCTION__);
        $data = DB::table('articulos')
                    ->select(
                        'id',
                        'name',
                        'presentacion'  
                    )
                    ->where('es_combo',true)
                    ->whereNull('deleted_at')
                    ->get();
            return view('admin.combos.index',['data' => $data]);
    }
    public function edit(Request $r){
        $articulos = DB::table('articulos')
        ->where('es_combo',false)
        ->whereNull('deleted_at')
        ->get();
        $select = DB::table('articulos')
        ->select("id")
        ->selectRaw("name||' '||name_co as label")
        ->whereNull('deleted_at')
        ->where('id' ,'<>',$r->id)
        ->get();
        $combo = DB::table('articulos')
        ->where('es_combo',true)
        ->where('id',$r->id)
        ->whereNull('deleted_at')
        ->first();

        $cargados = DB::table('combo_articulos')
        ->join('articulos', 'articulos.id', '=', 'combo_articulos.articulo_id')
        ->select("combo_articulos.articulo_id",'combo_articulos.cantidad')
        ->selectRaw("articulos.name as articulo")
        ->where('combo_articulos.combo_id',$r->id)
        ->whereNull('articulos.deleted_at')
        ->get();

        return view('admin.combos.edit',['articulos' => $articulos,'combo'=> $combo,'select'=> $select,'cargados'=> $cargados]);
    }
    
      
    public  function combosDetalles(Request $r){
        Log::info(__FUNCTION__);  $rta['cod'] = 500;$rta['msg'] = 'hubo un error inesperado';
        $datos= json_decode($r->datos);

        try {
        //antes de hacer el foreach eliminamos
        
        DB::table('combo_articulos')->where('combo_id', '=', $r->combo)->delete();
          foreach ($datos as $stock) {
            unset($data); 
            $data = Array(
                'combo_id'=> $r->combo,
                'articulo_id'=>intval( $stock->articulo),
                'cantidad' => intval ($stock->cantidad),   
                'created_at'=> 'now()',
                'created_by'=> auth()->user()->id
            ); 
            DB::table('combo_articulos')->insert($data);
            Log::info(DB::getQueryLog());
              $rta['cod'] = 200;
              $rta['msg'] = 'Insertado';
          }
          
        } catch (\Throwable $th) {
          Log::info('El query dio error =>'.$th->getMessage());
            $rta['msg'] = 'Error al insertar detalles del combo'; 
        }
        return $rta;
      }
      public function delete(Request $r){
        Log::info(__FUNCTION__."/".__FILE__);Log::info($r);$rta['cod']=500; $rta['msg']="ocurrio un Error inessperado";
        try {
          
            $D=DB::table('articulo_imagenes')->where('articulo_id', '=', $r->id)->delete();
            $l=DB::table('articulos_listas_precios')->where('articulo_id', '=', $r->id)->delete();
            DB::table('combo_articulos')->where('articulo_id', '=', $r->id)->delete();
            DB::table('combo_articulos')->where('combo_id', '=', $r->id)->delete();
            //puede que tenga o no detalles asi que no se si se puede eliminar
            DB::table('articulos')->where('id', '=', $r->id)->update([
              'deleted_at'=>now(),
              'deleted_by'=>Auth()->user()->id,
            ]);
            $dos=DB::table('articulos')->where('id', '=', $r->id)->delete();
            $rta['cod']=200; $rta['msg']="Los Detalles y el articulo fueron eliminados";

            return back()->with('status', $rta['msg']);
        } catch (\Throwable $th) {
          Log::info("error".$th->getMessage());
          // if(utilidadesController::strContains($th->getMessage(), 'foreign')){
            $rta['cod'] = 500; 
            $rta['msg'] = 'Los Detalles Fueron Eliminados pero el articulo esta asociado y no puede ser eliminado';
            return back()->withErrors($rta['msg']);
          // }
        }
      }
     
}
