<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\Input;

class ajustesController extends Controller
{
    public function index(){

        $empresas = DB::table('empresa')
        ->whereNull('deleted_at')
        ->get();
        $categorias = DB::table('categorias')
        ->whereNull('deleted_at')
        ->whereRaw(' id in (select categoria_id from articulos)')
        ->get();
        return view('admin.ajustes.index',['empresas' => $empresas,'categorias' => $categorias]);
    }
    public function colador(Request $r){
        Log::info(__FUNCTION__); 
        Log::info($r);
        $r->validate([
            'empresa' => 'required',
        ]); 
        $empresa =$r->empresa;
        $categoria = $r->categoria;
        $p = $r->categoria;
        $buscar=
        DB::table('articulos')
        ->join('categorias','categorias.id','=','articulos.categoria_id')
        ->where('empresa_id', '=', $r->empresa)
        ->select('articulos.id','articulos.existencia')
        ->selectRaw('categorias.name as name')
        ->when($p, function($query, $p){
            return $query->where('categoria_id', '=', $p);
        })
        ->whereNull('articulos.deleted_at')
        ->orderBy('articulos.id','asc')
        ->get();
        $empresas = DB::table('empresa')
        ->whereNull('deleted_at')
        ->get();
        $categorias = DB::table('categorias')
        ->whereNull('deleted_at')
        ->whereRaw(' id in (select categoria_id from articulos)')
        ->get();
        if (!empty($buscar)) {
            return view('admin.ajustes.index',['empresas' => $empresas,'categorias' => $categorias,'buscar' => $buscar,'empresa' => $empresa,'categoria' => $categoria ])->with('status', 'Listando');
        }else{
            return back()->withErrors('No se encontro ningun registro');
        }
        

    }
    public function guardar(Request $r){
        Log::info(__FUNCTION__);Log::info($r);
        $existencia=DB::table('articulos')
        ->select('id','existencia')
        ->where('empresa_id', '=', $r->empresa)
        ->where('categoria_id', '=', $r->categoria)
        ->whereNull('deleted_at')
        ->get();
        $error_vacio=null;
        $error_negativo=null;
        $error_letra=null;
        $succes =[];
        $traidos=$r->articulos;
        // dd($traidos);
        foreach($r->articulos as $art){
                if (!empty($art['cantidad']) || $art['cantidad'] != null) {
                    if (is_numeric($art['cantidad'])) {
                        if ($art['cantidad'] < 0) {
                            $error_negativo.=  "/ El #".$art['id']." No debe ser un numero negativo";
                        }else{
                            foreach ($existencia as $k => $val) {
                                if ($existencia[$k]->id == $art['id']) {
                                    if ($existencia[$k]->existencia != $art['cantidad']) {
                                        $succes[]=['name' => $art['id'] ,'actual' => $art['cantidad'] , "viejo" =>$existencia[$k]->existencia ];
                                    }
                                }
                            }
                        }
                    }else{
                        $error_letra.="/ El #".$art['id']." No puede puede contener caracteres solo numeros";
                    }
                    
                }else{
                    $error_vacio .= "/ El #".$art['id']." No puede estar vacio";
                }
            
        }
        if ($error_negativo != null || $error_vacio != null ||  $error_letra != null) {
            
            return back()->withInput($r->all())->withErrors($error_negativo."/".$error_vacio."/".$error_letra);
        }else{
        try {
        foreach ($succes as $ajustes => $value) {
            unset($data); 
            $data = Array(
                'articulo_id'=> $succes[$ajustes]['name'],
                'cantidad_anterior'=>( $succes[$ajustes]['viejo']),
                'cantidad_nueva' => ($succes[$ajustes]['actual']),   
                'created_at'=> 'now()',
                'created_by'=> Auth::user()->id
            ); 
                DB::table('ajustes')->insert($data);
                Log::info(DB::getQueryLog());
            }
            
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return back()->withErrors("Hubo un problema al querer insertar los articulos detalles");
        }
        try {
            foreach ($succes as $articulo  => $value) {
                DB::table("articulos")
                ->where("id", '=',$succes[$articulo]['name'])
                ->update([
                    'existencia' => $succes[$articulo]['actual'], 
                    'updated_at' => 'now()', 
                    'updated_by' => Auth::user()->id
                ]);
                Log::info(DB::getQueryLog());
            }
            return redirect('ajustes/')->with('status', 'Actualizado');
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            //debemos implementar el rollback
            return back()->withErrors("Hubo un problema al querer actualizar los articulos");
        }
       
     }
    }
}
