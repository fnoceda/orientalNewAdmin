<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UsersBajaController extends Controller
{
    public function index(){
        Log::info(__FUNCTION__.'/'.__FILE__);
            return view('admin.baja.index');
    }
    public function autenticarUsuario(Request $r){
        Log::info(__FUNCTION__.'/'.__FILE__);
        $validator = Validator::make($r->all() ,[
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:18',
        ]);
        try {
            $pass= DB::table('users')->where('email',$r->email)->first();
            if (isset($pass->password) && $pass->deleted_at == null) {
                if (Hash::check($r->password,$pass->password)) {
                    $dateTime = date('Y-m-d H:i:s');
                    $token = bcrypt($pass->access_token . '.' . $dateTime . '.' . rand()); 
                    DB::table('users')
                    ->where('email', '=', $r->email)
                    ->update([
                        'baja_token' => $token, 
                    ]);
                    Log::info(DB::getQueryLog());
                    return view('admin.baja.index')->with(['user'=>$pass]);
                }else{
                    return back()->withErrors("Usuario o contraseña equivocado");
                }
            }else{
                return back()->withErrors("No existe ese usuario");
            }
           
    } catch (\Throwable $th) {
        Log::info("aqui ocurrio un error".$th->getMessage());
        return back()->withErrors("ocurrion un Error inesperado");
    };
    }
    public function bajaUsuario(Request $r){
        Log::info(__FUNCTION__.'/'.__FILE__);
        try {
            $user= DB::table('users')->where('id',$r->user_id)->first();
            if (isset($user->id)) {
                DB::table('users')
                ->where('id', '=', $user->id)
                ->update([
                    'deleted_at' => 'now()', 
                    'deleted_by' => 1, 
                ]); 
                return view('admin.baja.index')->with('success','Usuario dado de baja Correctamente');
            }else{
                return redirect()->route('baja')->withErrors('Error.!, no autorizado');
            }
    } catch (\Throwable $th) {
        Log::info("aqui ocurrio un error".$th->getMessage());
        return back()->withErrors("ocurrion un Error inesperado");
    };
    }
    
}
