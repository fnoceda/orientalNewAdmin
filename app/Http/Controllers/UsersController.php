<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UsersController extends Controller
{
    
    public function index(){
        Log::info(__FUNCTION__."/".__FILE__);
        $usuarios=DB::table('users')
        ->select('users.*')
        ->where('users.perfil','<>','cliente')
        ->where('users.email','<>',"fnoceda83@gmail.com")
        ->get();
        $ciudades = DB::table('ciudades')->select('id', 'name','*')->get();
        $perfiles = DB::table('perfiles')->select('id', 'name','*')->get();
        $empresas = DB::table('empresa')->select('id', 'name','*')->get();
        return view('users.index', [
        'usuarios' => $usuarios,
        'ciudades' => $ciudades,
        'perfiles' => $perfiles,
        'empresas' => $empresas,
        ]);
    }
    public function usersList(){
        Log::info(__FUNCTION__."/".__FILE__);
        $usuarios=DB::table('users')
        ->select('users.*')
        ->where('users.perfil','<>','admin')
        ->where('users.email','<>',"fnoceda83@gmail.com")
        ->get();
        return view('users.clientes.index', [
        'usuarios' => $usuarios,
     
        ]);
    }
    public static function paginadoAjax(Request $request){
        if($request->ajax()){
            $usuarios = DB::table('users')
            ->select('users.*')
            ->where('users.perfil','<>','cliente')
            ->where('users.email','<>',"fnoceda83@gmail.com")
            ->get();
            return view('users.listar_datos', ['usuarios'=>$usuarios])->render();
        }
    }
    public static function agregar(Request $request){
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info('request'); Log::info($request);
        $response['code'] = 500; $response['message'] = 'Hubo un error inesperado';
        DB::enableQueryLog(); //kaka

        $validator = Validator::make($request->all() ,[
            'name'=> 'required',
            'email' => 'required|email|max:255|unique:users',
            'ruc'=> 'required',
            'telefono'=> 'required',
            'password' => 'required|min:6',
            // 'direccion'=> 'required',
            'ciudad_id'=> 'required',
            // 'direccion_delivery'=> 'required',
            // 'latitud'=> 'required',
            // 'longitud'=> 'required',
            'perfil'=> 'required',
        ]);

        if ($validator->fails()) {
            $response['message'] = 'Por favor complete todos los campos ' . $validator->errors();
        }else{
            if (Auth::user()->perfil !='admin') {
                $response['message']='Usuario inexistente en la web';
                return $response;
            }
            try {
                $datos = Array(
                    'name'=>$request->name,
                    'email' => $request->email,
                    'ruc'=>$request->ruc,
                    'password' => bcrypt($request->password),
                    'telefono'=> $request->telefono,
                    'direccion'=> $request->direccion,
                    'ciudad_id'=> $request->ciudad_id,
                    'direccion_delivery'=> $request->direccion_delivery,
                    'latitud'=> $request->latitud,
                    'longitud'=> $request->longitud,
                    'perfil' => $request->perfil,
                    'perfil_id' => $request->perfil_id,
                    'empresa_id' => $request->empresa_id ?? null,
                    'created_at'=>'now()',
                    'created_by'=> Auth::user()->id
                );
                $insertId = DB::table('users')->insertGetId($datos);
                $response['code'] = 200; $response['message'] = 'Usuario insertado exitosamente';
                Log::info(DB::getQueryLog());
            } catch (\Throwable $th) {
                Log::info('El query dio error =>'.$th->getMessage());
                $response['code'] = 500;
                $response['message'] = 'Error al insertar Usuario ';
                return $response;
            }
        }
        return $response;
    }

    public static function editar(Request $request){
        Log::info(__FUNCTION__); Log::info($request); $response['code'] = 500; $response['message'] = 'Hubo un error inesperado';
        Log::info(DB::enableQueryLog());

        try {

        if (empty($request->password) || ($request->password == null)) {
            $validator = Validator::make($request->all() ,[
                'name'=> 'required',
                'ruc'=> 'required',
                'telefono'=> 'required',
                'ciudad_id'=> 'required',
                'perfil'=> 'required',
            ]);
        }else{
            Log::info("si necesita");

            $validator = Validator::make($request->all() ,[
                'name'=> 'required',
                'ruc'=> 'required',
                'telefono'=> 'required',
                'password' => 'required|min:6',
                'ciudad_id'=> 'required',
                'perfil'=> 'required',
            ]);
        }
        if ($validator->fails()) {
            
            $response['message'] = 'Por favor complete todos los campos ' . $validator->errors();
        }else{
            if (Auth::user()->perfil !='admin') {
                $response['message']='Usuario inexistente en la web';
                return $response;
            }
            if (empty($request->password)) {
                $datos = DB::table('users')->where('id', '=', $request->id)
                ->update([
                    'name'=>$request->name,
                    'email' => $request->email,
                    'ruc'=>$request->ruc,
                    'telefono'=> $request->telefono,
                    'direccion'=> $request->direccion,
                    'ciudad_id'=> $request->ciudad_id,
                    'direccion_delivery'=> $request->direccion_delivery,
                    'latitud'=> $request->latitud,
                    'longitud'=> $request->longitud,
                    'perfil' => $request->perfil,
                    'perfil_id' => $request->perfil_id,
                    'empresa_id' => $request->empresa_id ?? null,
                    'updated_at'=>'now()',
                ]);
            }else{
                $datos = DB::table('users')->where('id', '=', $request->id)->update([
                    'name'=>$request->name,
                    'email' => $request->email,
                    'ruc'=>$request->ruc,
                    'password' => bcrypt($request->password),
                    'access_token' => null, 
                    'access_time' => null, 
                    'remember_token' => null,
                    'telefono'=> $request->telefono,
                    'direccion'=> $request->direccion,
                    'ciudad_id'=> $request->ciudad_id,
                    'direccion_delivery'=> $request->direccion_delivery,
                    'latitud'=> $request->latitud,
                    'longitud'=> $request->longitud,
                    'perfil' => $request->perfil,
                    'updated_at'=>'now()',
                ]);
            }
            Log::info(DB::getQueryLog());
            Log::info('Registros actualizado => ' . $datos);
            if($datos > 0){
                $response['code'] = 200; $response['message'] = 'Usuario actualizado exitosamente';
            }
        }

        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            $response['code'] = 500;
            $response['message'] = 'Error al actualizar ';
            if(isset($request->seccionPerfil)){
                return redirect()->back()->with('message',$response['message']);
            }else{
                return $response;
            }
        }
        if(isset($request->seccionPerfil)){
            return redirect()->back()->with('message',$response['message']);
        }else{
            return $response;
        }
        
    }
    public function cambiarContraseña(){
        DB::enableQueryLog(); Log::info(__FUNCTION__);
        $ciudades = DB::table('ciudades')->select('id', 'name','*')->get();

            return view('profile', [
                'ciudades' => $ciudades,
                ]);
    }
    public function guardarContraseña(Request $r){
        DB::enableQueryLog(); Log::info(__FUNCTION__); Log::info($r);

        $validator = Validator::make($r->all() ,[
            'new_password' => 'required|min:6|max:18',
            'password_confirmation' => 'required|same:new_password',
            'current_password'=>'required',
        ]);
        if ($validator->fails()) {
            return back()->withInput($r->all())->withErrors('Por favor complete todos los campos ' . $validator->errors());
        }else{
            if (Auth::user()->perfil !='admin') {
                return back()->withInput($r->all())->withErrors("Usuario inexistente en la web");
            }
            try {
                    if (Hash::check($r->current_password,Auth::user()->password)) {
                                $query = "
                                select id from users where email=:email and id=:id limit 1
                                ";
                                // $data = DB::select(DB::raw($query), array('id' =>$r->id_usuario,'email' =>Auth::user()->email));
                                $data = DB::select(($query), array('id' =>$r->id_usuario,'email' =>Auth::user()->email));
                                DB::table('users')
                                ->where('id', '=', $data[0]->id)
                                ->update([
                                        'remember_token' => null,
                                        'access_token' => null, 
                                        'access_time' => null, 
                                ]);
                                Log::info(DB::getQueryLog());
                                $datos = DB::table('users')->where('id', '=', $r->id_usuario)->update([
                                    'password'=>bcrypt($r->new_password),
                                    'remember_token' => null,
                                    'access_token' => null, 
                                    'access_time' => null, 
                                ]);
                                Log::info(DB::getQueryLog());
                                return redirect()->back()->with('message','Contraseña Actualizado');
                            }else{
                                return back()->withInput($r->all())->withErrors("Contraseña Actual Equivocada");
                            }
            } catch (\Throwable $th) {
                Log::info("aqui ocurrio un error".$th->getMessage());
                return back()->withInput($r->all())->withErrors("ocurrion un Error inesperado");
            }
        }

    }
    public function passReset(Request $r)
    {
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog(); $rta['cod'] = 500; $rta['msg'] = "error";

        $validator = Validator::make($r->all(), [
            'correo' => 'required|email'
        ]);
        $myMarkee = $r->correo.'_'.date("Y-m-d");
            if ($validator->fails()) {
                Log::info('Fallo la validacion');
                $rta['msg'] = UtilidadesController::arr2str($validator->errors());
                $rta['cod'] = 422;
            } else {
                $query = " select * from users where email = :email ";
                $rs = UtilidadesController::getFirst($query, array('email' => $r->correo));
                Log::info($rs);
                if (empty($rs['dat']->id)) { //Si esta vacio significa que la cuenta no existe
                    $rta['cod'] = 418;
                    $rta['msg'] = "La cuenta con el correo proporcionado no existe";
                }else{
                    $palabra = UtilidadesController::claveAleatoria(); Log::info($palabra);
                    $query = "update users set  password = :palabra, access_token = null, access_time = null, remember_token = null where id = :id ";
                    $data = Array('id' => $rs['dat']->id, 'palabra' => bcrypt($palabra));
                    if ($this->update($query, $data)) {
                        Log::info('Se cambio la clave exitosamente, procedemos a enviar correo');

                        $html = ' Su nueva clave de acceso es <b style="color:blue">' . $palabra . '</b> ';
                        Mail::send([], [], function ($mail) use ($r, $html) {
                        $mail
                            ->from(env('MAIL_USERNAME'), 'OrientalPY')
                            ->to($r->correo)
                            ->subject('CorPar App - Nueva Clave')
                            // ->setBody($html, 'text/html');
                             ->html($html);

                    });
                    if(count(Mail::failures()) > 0){
                        Log::error('Ocurrio un error al intentar enviar el correo');
                    }else{
                        $rta['cod'] = 200; $rta['msg'] = "OK";
                        Log::info('Fin del proceso, se envio correo al usuario con su nueva clave exitosamente!');
                    }

                    }
                }
            }

        Log::info($rta);
            if ( $rta['cod'] == 200) {
                return redirect('/login')->withErrors("Revise su correo");
            }else{
                return redirect('/login')->withErrors($rta['msg']);
            }
        
    }
    public static function update($query, $data)
    {
        //esta funcion actualiza registros y retorna el numero de registros actualizados
        DB::enableQueryLog();
        Log::info(__FILE__ . '/' . __FUNCTION__);
        $rta = 0;

        try {
            $rta = DB::update($query, $data);
            Log::info(DB::getQueryLog());
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException => ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Throwable => ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Exception => ' . $e->getMessage());
        }

        return $rta;
    }
}
