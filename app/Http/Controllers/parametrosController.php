<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class parametrosController extends Controller
{
    public function index(){
        Log::info(__FUNCTION__);
        
        $data = DB::table('parametros')->get();
        if (!empty($data)) {
            return view('/admin.parametros.index',['data' => $data]);
        }else{
            return redirect()->action('HomeController@index');
        }
    }
    public function create(Request $r){

    }
    public function edit(Request $r){
        Log::info(__FUNCTION__); Log::info($r);
        $data = DB::table('parametros')
        ->where('clave',$r->clave)
        ->first();
        if (!empty($data)) {
            return view('/admin.parametros.edit',['data' => $data]);
        }else{
            return redirect()->action('HomeController@index');
        }
        
    }
    public function update(Request $r){
        Log::info(__FUNCTION__); Log::info($r);
        if ($r->clave == 'contacto_whatsapp') {
            if ( (strlen(trim($r->valor)) < 12) || (strlen(trim($r->valor)) > 12) ) {
                    return back()->withErrors('Error en el numero telefonico, verifique e intentelo nuevamente evite los espacios');
            }else{
                $cadena=strval($r->valor);
                if ($cadena[0] == 5) {
                    if ($cadena[1] == 9) {
                        if ($cadena[2] == 5) {
                            if ($cadena[3] == 9) {
                                $r->validate([
                                    'valor' => 'required|numeric',
                                ]);
                               
                            }else{
                                return back()->withErrors('El numero telefonico Debe tener estas caracteristicas 595991747594');
                            }
                        }else{
                            return back()->withErrors('El numero telefonico Debe tener estas caracteristicas 595991747594');
                        }
                    }else{
                        return back()->withErrors('El numero telefonico Debe tener estas caracteristicas 595991747594');
                    }
                }else{
                    return back()->withErrors('El numero telefonico Debe tener estas caracteristicas 595991747594');
                }
            }
        }elseif($r->clave == 'contacto_mail'){
            $r->validate([
                'valor' => 'required|email',
            ]);
            
        }elseif($r->clave == 'precio_delivery'){
            $r->validate([
                'valor' => 'required|numeric',
            ]);
        }else{
            $r->validate([
                'valor' => 'required',
            ]);
        }
        $array=[
            'valor' =>$r->valor,
            ];
            try {
                $update=DB::table("parametros")
                ->where("clave", '=', trim($r->clave))
                ->update($array);
                Log::info(DB::getQueryLog());
                if ($update > 0) {
                    return redirect()->action('parametrosController@index');
                }else{
                    return back()->withErrors('Hubo un problema en el proceso');
                }
            } catch (\Throwable $th) {
                // throw $th;
                Log::info('El query dio error =>'.$th->getMessage());
                return back()->withErrors('Hubo un problema en el proceso');
            }
        

    }
}
