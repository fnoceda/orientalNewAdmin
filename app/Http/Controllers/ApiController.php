<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ApiController extends Controller
{
    public function getCiudades(Request $r)
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $query = "select id, name,updated_at  from ciudades order by name";
        // $data = DB::select(DB::raw($query));
        $data = DB::select(($query));
        //  $data = UtilidadesController::getQuery($query);
        return response()->json($data);
    }

    public function getBarrios(Request $r)
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $query = "select id, ciudad_id, name,updated_at  from barrios order by 1";
        // $data = DB::select(DB::raw($query));
        $data = DB::select(($query));
        return response()->json($data);
    }

    public function cambiarClave(Request $r){
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r);  $rta['cod'] = 500; $rta['msg'] = "Ocurrio un error de proceso, por favor intente mas tarde";

        $validator = Validator::make($r->all(), [
            'correo' => 'required|email',
            'clave_actual' => 'required|min:5|max:18',
            'clave_nueva'=>'required'
        ]);

        if ($validator->fails()) {
            Log::info('Fallo la validacion');
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            $user = self::getUser($r->header('token'));
            if (empty($user->id)) {
                $rta['cod'] = 418;
                $rta['msg'] = "I'm a TeapoT";
            }else{
                $admin=DB::table('users')->where('id', '=', $user->id)->first();
                if ($admin->perfil == 'cliente') {
                    if (Hash::check($r->clave_actual, $user->password)) {
                        DB::table('users')->where('id', '=', $user->id)->update(['remember_token' => null]);
                        Log::info(DB::getQueryLog());
                        $cuantos=DB::table('users')->where('id', '=', $user->id)->update(['password' => bcrypt($r->clave_nueva), 'updated_at' => 'now()', 'updated_by' => $user->id ]);
                        Log::info(DB::getQueryLog());
                        if($cuantos > 0){ $rta['cod'] = 200; $rta['msg'] = "Su clave ha sido modificada con exito"; }

                    }else{
                        $rta['cod'] = 418;
                        $rta['msg'] = "Clave Actual no Corresponde";
                    }
                }else{
                    $rta['cod'] = 422;
                    $rta['msg'] = "Usuario no existe en la app";
                }

            }
        }


        Log::info($rta);
        return response()->json($rta);
    }


    public function passReset(Request $r)
    {
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog(); $rta['cod'] = 500; $rta['msg'] = "error";

        $validator = Validator::make($r->all(), [
            'correo' => 'required|email'
        ]);
        $myMarkee = $r->correo.'_'.date("Y-m-d"); ;
        if( $r->headers->get('markee') == md5($myMarkee) ){
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
                            ->setBody($html, 'text/html');
                    });
                    
                       
                        $rta['cod'] = 200; $rta['msg'] = "OK";
                        Log::info('Fin del proceso, se envio correo al usuario con su nueva clave exitosamente!');
                    

                    }
                }
            }
        }else{
            Log::info($r->headers->get('markee')+'!='+md5($myMarkee));
            $rta['cod'] = 418;
            $rta['msg'] = "La cuenta con el correo proporcionado no existe";
        }

        Log::info($rta);
        return response()->json($rta);
    }

    public function fbLogin(Request $r)
    {
        //Esta funcion, si ya existe el usuario retorna un doLogin
        //Si no existe retorna un userStore
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog(); $rta['cod'] = 500; $rta['msg'] = "error";

        $validator = Validator::make($r->all(), [
            'name' => 'required',
            'correo' => 'required|email',
            'password' => 'required',
            'telefono'=>'required'
        ]);
        if ($validator->fails()) {
            Log::info('Fallo la validacion');
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            $query = " select u.id, u.ruc, u.password,  u.name, u.email as correo, u.telefono, u.direccion, u.access_token as token, u.latitud, u.longitud,
                            c.id as ciudad_id, c.name as ciudad_name,
                            b.id as barrio_id, b.name as barrio_name,
                            --( now() + interval '24h' ) as expires_in
                            (u.access_time + interval '6 months') AS expires_in

                            from users u
                            left join ciudades c on  u.ciudad_id = c.id
                            left join barrios b on  u.barrio_id = b.id
                            where email = :correo and u.deleted_at is null ";
            $data = array('correo' => $r->correo);
            $rs = $this->getFirst($query, $data);
            $user = $rs['dat'];
            if (empty($user->password)) {
                Log::info('esta vacio el resulset');
                return self::userStore($r);
            } else {
                Log::info('hay data');
                if (Hash::check($r->password, $user->password)) {
                    Log::info('clave coincide');

                    $dateTime = date('Y-m-d H:i:s');
                    $token = bcrypt($user->token . '.' . $dateTime . '.' . rand()); //concateno anteriorAcessToken + fecha/hora/minuto/segundo + random
                    $query = " update users set access_token = :token, access_time = '" . $dateTime . "' where id = :id ";
                    $data = array('token' => $token, 'id' => $user->id);
                    if ($this->update($query, $data)) {
                        $rta['cod'] = 200;
                        $rta['msg'] = 'OK';
                        $user->token = $token;
                        $rta['dat'] = $user;
                        unset($rta['dat']->password);
                    }else{
                        Log::info('fallo el update');

                    }
                } else {
                    Log::info('clave no coincide =>' . trim($r->clave));
                    Log::info('DB=>' . $user->password);
                    Log::info('RQ=>' . bcrypt($r->clave));

                    $rta['cod'] = 401;
                    $rta['msg'] = 'Credenciales invalidas';
                }
            }
        }
        Log::info(__FUNCTION__ . ' RTA => ');
        Log::info($rta);
        return response()->json($rta);
    }


    public function appleLogin(Request $r){
        //Esta funcion, si ya existe el usuario retorna un doLogin
        //Si no existe retorna un userStore
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog(); $rta['cod'] = 500; $rta['msg'] = "error";

        $validator = Validator::make($r->all(), ['id' => 'required']);
        if ($validator->fails()) {
            Log::info('Fallo la validacion');
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            $query = " select u.id, u.ruc, u.password,  u.name, u.email as correo, u.telefono, u.direccion, u.access_token as token, u.latitud, u.longitud,
                            c.id as ciudad_id, c.name as ciudad_name,
                            b.id as barrio_id, b.name as barrio_name,
                            --( now() + interval '24h' ) as expires_in,
                             (u.access_time + interval '6 months') AS expires_in,
                            user_access_id
                            from users u
                            left join ciudades c on  u.ciudad_id = c.id
                            left join barrios b on  u.barrio_id = b.id
                            where user_access_id = :id and u.deleted_at is null ";
            $data = array('id' => $r->id);
            $rs = $this->getFirst($query, $data);
            $user = $rs['dat'];
            if (empty($user->password)) {
                Log::info('esta vacio el resulset');
                return self::userStore($r);
            } else {
                Log::info('hay data');
                if (Hash::check($r->password, $user->password)) {
                    Log::info('clave coincide');
                    $dateTime = date('Y-m-d H:i:s');
                    $token = bcrypt($user->token . '.' . $dateTime . '.' . rand()); //concateno anteriorAcessToken + fecha/hora/minuto/segundo + random
                    $query = " update users set access_token = :token, access_time = '" . $dateTime . "' where id = :id ";
                    $data = array('token' => $token, 'id' => $user->id);
                    if ($this->update($query, $data)) {
                        $rta['cod'] = 200;
                        $rta['msg'] = 'OK';
                        $user->token = $token;
                        $rta['dat'] = $user;
                        unset($rta['dat']->password);
                    }else{
                        Log::info('fallo el update');

                    }
                } else {
                    Log::info('clave no coincide =>' . trim($r->clave));
                    Log::info('DB=>' . $user->password);
                    Log::info('RQ=>' . bcrypt($r->clave));

                    $rta['cod'] = 401;
                    $rta['msg'] = 'Credenciales invalidas';
                }
            }
        }
        Log::info(__FUNCTION__ . ' RTA => ');
        Log::info($rta);
        return response()->json($rta);
    }



    public static function userStore(Request $r)
    {

        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog(); $rta['cod'] = 500; $rta['msg'] = "error";

        $validator = Validator::make($r->all(), [
            'name' => 'required',
            'correo' => 'required|email',
            'telefono' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            Log::info('Fallo la validacion');
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            $user=User::where('email',$r->input('correo'))->whereNotNull('deleted_at')->first();
            if (!$user) {
                $user = new User();
                Log::info('crea');
            }else{
                Log::info('actualiza');

                $user->deleted_at = null;
                $user->deleted_by = null;
                $user->delete_contra=null;
            }
            $user->name = $r->input('name');
            $user->ruc = $r->input('ruc');
            $user->telefono = $r->input('telefono');
            $user->direccion = $r->input('direccion');
            $user->email = $r->input('correo');
            $user->password = bcrypt($r->input('password'));
            $user->ciudad_id = $r->input('ciudad');
            $user->direccion_delivery = $r->input('direccion');
            $user->direccion = $r->input('direccion');
            $user->latitud = $r->input('latitud');
            $user->longitud = $r->input('longitud');
            $user->perfil = "cliente";
            $user->user_access_id = ( empty($r->id) ) ? '' : $r->id ;
            $user->created_at = now();
            $user->created_by = 1;
            try {
                if ($user->save()) {
                    $dateTime = date('Y-m-d H:i:s');
                    $token = bcrypt($user->access_token . '.' . $dateTime . '.' . rand()); //concateno anteriorAcessToken + fecha/hora/minuto/segundo + random
                    $query = " update users set access_token = :token, access_time = '" . $dateTime . "' where id = :id ";
                    $data = array('token' => $token, 'id' => $user->id);
                    if (self::update($query, $data)) {
                        $rta['cod'] = 200;
                        $rta['msg'] = 'OK';
                        $query = " select u.id, u.ruc,  u.name, u.email as correo, u.telefono, u.direccion, u.access_token as token, u.latitud, u.longitud,
                                    c.id as ciudad_id, c.name as ciudad_name, b.id as barrio_id, b.name as barrio_name, 
                                    --( now() + interval '24h' ) as expires_in,
                                     (u.access_time + interval '6 months') AS expires_in, 
                                     user_access_id
                                    from users u
                                    left join ciudades c on  u.ciudad_id = c.id
                                    left join barrios b on  u.barrio_id = b.id
                                    where u.id = :id ";
                        $data = array('id' => $user->id);
                        $rs = self::getFirst($query, $data);
                        $rta['dat'] = $rs['dat'];
                    }
                } else {
                    $rta['cod'] = 500;
                }
            } catch (\Exception $e) {
                Log::error(__FUNCTION__ . '=>' . $e->getMessage());
                $pos = strpos($e->getMessage(), 'violates unique');
                $rta['cod'] = 501;
                if ($pos !== false) {
                    $rta['cod'] = 409;
                    $rta['msg'] = 'Error de proceso';
                }
            }
        }
        Log::info($rta);
        return response()->json($rta);
        // return $rta;
    }

    public function doLogin(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        Log::info($r);
        $data = null;
        $rta['cod'] = 500;
        $rta['msg'] = 'Error de proceso';

        $validator = Validator::make($r->all(), [
            'correo' => 'required|email',
            'clave' => 'required',
        ]);
        if ($validator->fails()) {
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            try {
                $query = " select u.id, u.ruc, u.password,  u.name, u.email as correo, u.telefono, u.direccion, u.access_token as token, u.latitud, u.longitud,
                            c.id as ciudad_id, c.name as ciudad_name,
                            b.id as barrio_id, b.name as barrio_name,
                            --( now() + interval '24h' ) as expires_in
                             (u.access_time + interval '6 months') AS expires_in
                            from users u
                            left join ciudades c on  u.ciudad_id = c.id
                            left join barrios b on  u.barrio_id = b.id
                            where email = :correo and u.deleted_at is null ";
                $data = array('correo' => $r->correo);
                $rs = $this->getFirst($query, $data);
                $user = $rs['dat'];
                if (empty($user->password)) {
                    $rta['cod'] = 404;
                    $rta['msg'] = 'Usuario no existe';
                } else {
                    if (Hash::check($r->clave, $user->password)) {
                        $dateTime = date('Y-m-d H:i:s');
                        $token = bcrypt($user->token . '.' . $dateTime . '.' . rand()); //concateno anteriorAcessToken + fecha/hora/minuto/segundo + random
                        $query = " update users set access_token = :token, access_time = '" . $dateTime . "' where id = :id ";
                        $data = array('token' => $token, 'id' => $user->id);
                        if (self::update($query, $data)) {
                            $rta['cod'] = 200;
                            $rta['msg'] = 'OK';
                            $user->token = $token;
                            $rta['dat'] = $user;
                            unset($rta['dat']->password);
                        }
                    } else {
                        Log::info('clave=>' . trim($r->clave));
                        Log::info('DB=>' . $user->password);
                        Log::info('RQ=>' . bcrypt($r->clave));

                        $rta['cod'] = 401;
                        $rta['msg'] = 'Credenciales invalidas';
                    }
                }
            } catch (\Exception $e) {
                Log::error(__FUNCTION__ . '=>' . $e->getMessage());
                $rta['cod'] = 500;
                $cod['msg'] = 'Error inesperado';
            }
        }
        return response()->json($rta);
    }

    public function refreshToken(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        Log::info($r);
        $data = null;
        $rta['cod'] = 0;
        $rta['msg'] = 'Error de proceso';
        $user = self::getUser($r->headers->get('token'));

        if (empty($user)) {
            $rta['msg'] = "Error de autenticacion";
        } else {
            $dateTime = date('Y-m-d H:i:s');
            $token = bcrypt($user['dat']->access_token . '.' . $dateTime . '.' . rand()); //concateno anteriorAcessToken + fecha/hora/minuto/segundo + random
            $query = " update users set access_token = :token, access_time = '" . $dateTime . "' where id = :id ";
            $data = array('token' => $token, 'id' => $user['dat']->id);
            if ($this->update($query, $data)) {
                $rta['cod'] = 1;
                $rta['msg'] = 'OK';
                $rta['dat']['token'] = $token;
                $rta['dat']['expires_in'] = env('ACCESS_EXPIRES');
            }
        }
        return $rta;
    }

    private static function getUser($token)
    { //busca el usuario del token
        Log::info(__FILE__ . '/' . __FUNCTION__);
        $data = null;
        $rta['cod'] = 0;
        $rta['msg'] = 'Error en proceso';

        if (!empty($token) || !empty($app)) {
            $query = " select * from users where access_token = :token and now() <= ( access_time + interval '" . env('HORAS_SESSION') . " hours') ";
            $data = array('token' => $token);
            $rta = ApiController::getFirst($query, $data);
        }

        return $rta['dat'];
    }
    public static function delete($tabla, $id)
    {
        //Elimina un registro de la base de datos
        DB::enableQueryLog(); //Log::info(__FILE__.'/'.__FUNCTION__);
        $rta = false;

        try {

            if (DB::table($tabla)->where('id', '=', $id)->delete()) {
                $rta = true;
                Log::info(DB::getQueryLog());
            } else {
                Log::info('Insert no dio error pero retorna false');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException => ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Throwable => ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Exception => ' . $e->getMessage());
        }

        return $rta;
    }

    public static function rollback_ventas_detalles($table, $indicador, $id)
    {
        //Elimina varios registros en caso de que existiesen
        DB::enableQueryLog(); //Log::info(__FILE__.'/'.__FUNCTION__); $rta = false;
        try {

            if ((DB::table($table)->where($indicador, '=', $id)->delete()) > 0) {
                $rta = true;
                Log::info(DB::getQueryLog());
            } else {
                $rta = true;
                Log::info('delete no dio error pero retorna false');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException => ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Throwable => ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Exception => ' . $e->getMessage());
        }

        return $rta;
    }

    public  function deleteUser(Request $r){
        Log::info(__FUNCTION__); $rta['msg'] = 'Error inesperado'; $rta['cod'] = 422;
         //Elimina usuario ligeramente

         try { // kaka
            DB::enableQueryLog();

            $user = self::getUser($r->header('token'));

            if (!empty($user->id)) {
                Log::info($user->id);

                $palabra = UtilidadesController::claveAleatoria();
                $query = "update users set  delete_contra = :palabra, access_time = null, remember_token = null where id = :id ";
                $enc=bcrypt($palabra);
                $data = Array('id' => $user->id, 'palabra' => $enc);
                if ( $this->update($query, $data)) {
                    $correo = 'contacto.corpar@gmail.com';
                    $html = '';
                    $html .= 'Hola <br />';
                    $html .= 'Sentimos que quiras eliminar tu cuenta :( <br />';
                    $html .= 'Por favor ingresa a este link para Eliminar tu cuenta '."<a href='https://oriental.soluciones.dev/drop/user?key=".$enc."'>Click Aqui</a><br/>";
                    $html .= 'Que tengas buen resto de jornada<br /><br /><br />';
                    $html .= 'OrientalPyApp';
                    Mail::send([], [], function ($mail) use ($correo, $html,$user) {
                        $mail
                            ->from($correo, 'OrientalPy')
                            ->to($user->email )
                            // ->to('alandiaz719@gmail.com')
                            ->subject('OrientalPY - petición de baja: ')
                            ->setBody($html, 'text/html');
                    });

                   
                        $rta['cod'] = 200;
                        $rta['msg']='Fin del proceso, se envio correo al usuario verifique para poder eliminar su cuenta!';
                    
                }
            }else{
                Log::info("no existe el usuario para el token => " . $r->header('token'));
            }



                 //enviamos un correo al usuario

         } catch (\Illuminate\Database\QueryException $e) {
             Log::error('QueryException => ' . $e->getMessage());
         } catch (Throwable $e) {
             Log::error('Throwable => ' . $e->getMessage());
         } catch (Exception $e) {
             Log::error('Exception => ' . $e->getMessage());
         }

         return $rta;
     }

    public  function deleteUser0(Request $r){
       Log::info(__FUNCTION__); $rta['msg'] = 'Error inesperado'; $rta['cod'] = 422;
        //Elimina usuario ligeramente
        $validator = Validator::make($r->all(), [
            'id' => 'required',
        ]);
        try {
        if ($validator->fails()) {
            $rta['msg'] = 'Error inesperado';
            $rta['cod'] = 422;
        } else {
            DB::enableQueryLog();
            $user=User::where('id',$r->id)->first();
            if (!empty($user)) {
                $palabra = UtilidadesController::claveAleatoria();
                $query = "update users set  delete_contra = :palabra, access_time = null, remember_token = null where id = :id ";
                $enc=bcrypt($palabra);
                $data = Array('id' => $r->id, 'palabra' => $enc);
                if ( $this->update($query, $data)) {
                    $correo = 'contacto.corpar@gmail.com';
                    $html = '';
                    $html .= 'Hola <br />';
                    $html .= 'Sentimos que quiras eliminar tu cuenta :( <br />';
                    $html .= 'Por favor ingresa a este link para Eliminar tu cuenta '."<a href='https://oriental.soluciones.dev/drop/user?key=".$enc."'>Click Aqui</a><br/>";
                    $html .= 'Que tengas buen resto de jornada<br /><br /><br />';
                    $html .= 'OrientalPyApp';
                    Mail::send([], [], function ($mail) use ($correo, $html,$user) {
                        $mail
                            ->from($correo, 'OrientalPy')
                            ->to($user->email )
                            // ->to('alandiaz719@gmail.com')
                            ->subject('OrientalPY - petición de baja: ')
                            ->setBody($html, 'text/html');
                    });

                   
                        $rta['cod'] = 200;
                        $rta['msg']='Fin del proceso, se envio correo al usuario verifique para poder eliminar su cuenta!';
                    
                }
            }
        }
                //enviamos un correo al usuario

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('QueryException => ' . $e->getMessage());
             $envio = false;
        } catch (Throwable $e) {
            Log::error('Throwable => ' . $e->getMessage());
             $envio = false;
        } catch (Exception $e) {
            Log::error('Exception => ' . $e->getMessage());
             $envio = false;
        }

        return $rta;
    }



    public static function converUtf8($data)
    {
        //esta funcion retorna el resultado de convert_from_latin1_to_utf8_recursively
        return self::convert_from_latin1_to_utf8_recursively($data);
    }
    private static function convert_from_latin1_to_utf8_recursively($dat)
    {
        //Esta funcion convierte de latin1 a utf8
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) {
                $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);
            }

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) {
                $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);
            }

            return $dat;
        } else {
            return $dat;
        }
    }
    public static function getFirst($query, $parametros = null)
    {
        //esta funcion retorna un bojeto solo con el primer registro
        DB::enableQueryLog();
        Log::info(__FILE__ . '/' . __FUNCTION__);
        $rta['cod'] = 0;
        $rta['msg'] = 'Error de proceso';

        try {
            if (empty($parametros)) {
                // $data = DB::select(DB::raw($query));
                $data = DB::select(($query));
                //  $data = UtilidadesController::getQuery($query);
            } else {
                //Log::info('hay parametros');
                foreach ($parametros as $key => $val) {Log::info($key . '=>' . $val);}
                // $data = DB::select(DB::raw($query), $parametros);
                $data = DB::select(($query), $parametros);
                
                Log::info(DB::getQueryLog());
            }
            //Log::info(DB::getQueryLog());
            $rta['cod'] = 1;
            $rta['msg'] = 'OK';
            $rta['reg'] = count($data);
            if (!empty($data)) {
                $rta['dat'] = $data[0];
            } else {
                $rta['dat'] = null;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $rta['cod'] = 0;
            $rta['msg'] = '1 Ocurrio un error al realizar la consulta';
            $rta['reg'] = 0;
            $rta['dat'] = null;
            Log::error($rta['msg'] . ' => ' . $e->getMessage());
        } catch (Throwable $e) {
            $rta['cod'] = 0;
            $rta['msg'] = '3 Ocurrio un error fatal al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = null;
            Log::error($rta['msg'] . ' => ' . $e->getMessage());
        } catch (Exception $e) {
            $rta['cod'] = 0;
            $rta['msg'] = '4 Ocurrio un error al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = null;
            Log::error($rta['msg'] . ' => ' . $e->getMessage());
        }
        Log::info($rta);
        return $rta;
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

    public function getCategoriasAnidado()
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $query = " select c.id, c.name, c.name_co as nameco, c.padre, i.path as icono from categorias c left join iconos i on c.icono_id = i.id where padre is null order by 2";
        $data = UtilidadesController::getQuery($query);
        foreach ($data['dat'] as $key => $val) {
            $data['dat'][$key]->hijos = self::getCategoriasHijos($val->id);
        }
        //return response()->json($this->converUtf8($data));
        return response()->json($data);
    }

    private static function getCategoriasHijos($id)
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $query = " select c.id, c.name, c.name_co as nameco, c.padre, i.path as icono
                    from categorias c left join iconos i on c.icono_id = i.id where padre = " . $id . " order by 2 ";
        // $data = DB::select(DB::raw($query));
        $data = DB::select(($query));
        foreach ($data as $key => $val) {
            $data[$key]->hijos = self::getCategoriasHijos($val->id);
        }
        return $data;
    }

    public function getCategorias()
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;

            $query = " select c.id, initcap(c.name) as name, c.name_co as nameco, c.padre, i.path as icono, c.path as foto, categoriaprincipal(c.id) as principal,c.updated_at, c.orden
                        from categorias c left join iconos i on c.icono_id = i.id
                        where c.activo = true
                        order by orden
            ";


        $rta = UtilidadesController::getQuery($query);
        Log::info( $rta );
        //return response()->json($this->converUtf8($rta));
        return response()->json($rta);

    }

    public function get6Categorias(){
    $rta['cod'] = 500; $rta['msg'] = 'Ocurrio un error al realizar la consulta'; $rta['reg'] = 0; $rta['dat'] = NULL;
    try {
        DB::enableQueryLog();
        Log::info(__FILE__ . '/' . __FUNCTION__);

        // Obtener las categorías principales usando el Query Builder
        $categorias = DB::table('categorias as c')
            ->select(DB::raw('DISTINCT c.id, INITCAP(c.name) AS name, c.name_co AS nameco, 
                              c.padre, i.path AS icono, c.path AS foto, c.updated_at, c.orden'))
            ->leftJoin('iconos as i', 'c.icono_id', '=', 'i.id')
            ->where('c.activo', true)
            ->whereIn('c.id', function($query) {
                $query->select(DB::raw('DISTINCT padre'))
                      ->from('categorias')
                      ->whereIn('id', function($query) {
                          $query->select(DB::raw('DISTINCT categoria_id'))
                                ->from('articulos');
                      })
                      ->whereNotNull('padre');
            })
            ->orderBy('c.orden')
            ->get();
        if ($categorias->isEmpty()) {
            return response()->json(['error' => 'No hay categorías disponibles'], 404);
        }

        $datosFinales = [];

        foreach ($categorias as $categoria) {
            // Obtener los 6 primeros artículos de la categoría
            $articulos = DB::table('articulos as a')
                ->select(DB::raw('a.id, a.categoria_id AS categoriaid, a.empresa_id AS empresaid, a.etiqueta_id AS etiquetaid, 
                                  INITCAP(a.name) AS name, INITCAP(c.name) AS categoria, INITCAP(e.name) AS empresa, 
                                  x.path AS etiqueta, a.descripcion, a.presentacion, a.name_co AS nameco, 
                                  a.descripcion_co AS descripcionco, a.valoracion, a.observaciones, 
                                  a.observaciones_co AS observacionesco, a.existencia, a.es_combo AS escombo, a.por_gramo, 
                                  a.unidad_de_medida AS unidadmedida, a.timer_precio AS timerprecio, a.timer_desde AS timerdesde, 
                                  a.timer_hasta AS timerhasta, a.plazo_entrega AS plazoentrega, a.precio_antes AS precioantes, 
                                  a.precio_venta AS precioventa, a.updated_at, (round(random() * 1 + 1) + a.id)::int AS ventas, 
                                  a.colores, a.sabores, a.medidas'))
                ->join('empresa as e', 'a.empresa_id', '=', 'e.id')
                ->join('categorias as c', 'c.id', '=', 'a.categoria_id')
                ->leftJoin('etiquetas as x', 'x.id', '=', 'a.etiqueta_id')
                ->where('a.es_activo', true)
                ->where('e.es_activo', true)
                ->where('c.activo', true)
                ->where('a.categoria_id', $categoria->id)
                ->orderByDesc('c.orden')
                ->limit(6)
                ->get();

            // Si no hay artículos para la categoría principal, buscar en las subcategorías
            if ($articulos->isEmpty()) {
                // Buscar subcategorías de la categoría principal
                $subcategorias = DB::table('categorias')
                    ->select('id')
                    ->where('padre', $categoria->id)
                    ->get();

                $subcategoriaIds = $subcategorias->pluck('id');

                if ($subcategoriaIds->isNotEmpty()) {
                    // Buscar los 6 artículos de las subcategorías
                    $articulosSubcategorias = DB::table('articulos as a')
                        ->select(DB::raw('a.id, a.categoria_id AS categoriaid, a.empresa_id AS empresaid, a.etiqueta_id AS etiquetaid, 
                                          INITCAP(a.name) AS name, INITCAP(c.name) AS categoria, INITCAP(e.name) AS empresa, 
                                          x.path AS etiqueta, a.descripcion, a.presentacion, a.name_co AS nameco, 
                                          a.descripcion_co AS descripcionco, a.valoracion, a.observaciones, 
                                          a.observaciones_co AS observacionesco, a.existencia, a.es_combo AS escombo, a.por_gramo, 
                                          a.unidad_de_medida AS unidadmedida, a.timer_precio AS timerprecio, a.timer_desde AS timerdesde, 
                                          a.timer_hasta AS timerhasta, a.plazo_entrega AS plazoentrega, a.precio_antes AS precioantes, 
                                          a.precio_venta AS precioventa, a.updated_at, (round(random() * 1 + 1) + a.id)::int AS ventas, 
                                          a.colores, a.sabores, a.medidas'))
                        ->join('empresa as e', 'a.empresa_id', '=', 'e.id')
                        ->join('categorias as c', 'c.id', '=', 'a.categoria_id')
                        ->leftJoin('etiquetas as x', 'x.id', '=', 'a.etiqueta_id')
                        ->where('a.es_activo', true)
                        ->where('e.es_activo', true)
                        ->where('c.activo', true)
                        ->whereIn('a.categoria_id', $subcategoriaIds)
                        ->orderByDesc('c.orden')
                        ->limit(6)
                        ->get();

                    foreach ($articulosSubcategorias as $articulo) {
                        // Obtener imágenes del artículo
                        $categoria->articulo_imagen[ $articulo->id] = DB::table('articulo_imagenes')
                            ->select(DB::raw('id, articulo_id as articuloId, path as name, orden, updated_at'))
                            ->where('articulo_id', $articulo->id)
                            ->orderBy('orden')
                            ->limit(1)
                            ->get();
                    }

                    $categoria->articulos = $articulosSubcategorias;
                }
            } else {
                $categoria->articulos = $articulos;
            }

            $datosFinales[] = $categoria;
        }
       
        return response()->json($datosFinales);
    } catch (\Throwable $th) {
        Log::error("Error en get6Categorias: " . $th->getMessage());
        return response()->json($rta);
    }
}
    

    



    public function getBanners()
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 500;
        $query = " select b.id, b.path as name, b.destino as origen, s.page as destino, b.parametro
            from banners b join secciones s on s.id = b.seccion_id
            where b.deleted_at is null  and es_activo = true ";
        $rta = UtilidadesController::getQuery($query);
        return response()->json($this->converUtf8($rta), $rta['cod']);
    }

    public function getArticulos(Request $r)
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        //Agrego comentario para que tome el git mis cambios
        $query = " select a.id,
                        a.categoria_id as categoriaid,
                        a.empresa_id as empresaid,
                        a.etiqueta_id as etiquetaid,
                        initcap(a.name) as name,
                        initcap(c.name) as categoria,
                        initcap(e.name) as empresa,
                        x.path as etiqueta,
                        a.descripcion,
                        a.presentacion,
                        a.name_co as nameco,
                        a.descripcion_co as descripcionco,
                        a.valoracion,
                        a.observaciones,
                        a.observaciones_co as observacionesco,
                        a.existencia,
                        a.es_combo as escombo,
                        a.por_gramo,
                        a.unidad_de_medida as unidadmedida,
                        timer_precio as timerprecio,
                        timer_desde as timerdesde,
                        timer_hasta as timerhasta,
                        plazo_entrega as plazoentrega,
                        precio_antes as precioantes,
                        precio_venta as precioventa,
                        a.updated_at,
                        (round(random() * 1 + 1) + a.id)::int as ventas,
                        colores, sabores, medidas
                    from articulos a
                        join empresa e on a.empresa_id = e.id
                        join categorias c on c.id = a.categoria_id
                        left join etiquetas x on x.id = a.etiqueta_id
                    where a.es_activo = true and e.es_activo = true and c.activo = true
                    order by c.padre, c.orden desc ";

        if (isset($r->articulo)) {$query .= " and a.id = " . $r->articulo;}

        $data = UtilidadesController::getQuery($query);
        return response()->json($data);

    }

    public function articulosPrecios()
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $query = " select x.articulo_id as articuloid, l.name, l.name_co as nameco, x.costo as precio, cantidad from articulos_listas_precios x join listas_precios l on l.id = x.lista_id where x.costo > 0 ";
        $data = UtilidadesController::getQuery($query);
        return response()->json($data);
    }

    public function articulosImagenes()
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $rta['msg'] = 'OK';
        $query = " select id, articulo_id as articuloId, path as name, orden,updated_at from articulo_imagenes order by orden ";
        $rta = UtilidadesController::getQuery($query);
        return response()->json($rta);
    }

    public function userUpdate(Request $r)
    {
        Log::info(__FILE__.'/'.__FUNCTION__);
        Log::info('------REQUEST------');
        Log::info($r);
        Log::info('------REQUEST------');

        $rta['cod'] = 500;
        $rta['reg'] = 0;
        $rta['msg'] = 'Error de proceso';

        $user = self::getUser($r->header('token'));
        // Log::info($user);

        if (!empty($user->id)) {
            Log::info('user=>' . $user->id);
            Log::info('r=>' . $r->id);

            if ($user->id == $r->id) {
                $query = " update users set
                    ruc = :ruc,
                    name = :name,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    latitud = :latitud,
                    longitud = :longitud,
                    ciudad_id = :ciudad_id,
                    barrio_id = :barrio_id,
                    updated_at = now(),
                    updated_by = :updated_by
                where id = :id";

                $data = array(
                    'id' => $user->id,
                    'ruc' => $r->ruc,
                    'name' => $r->name,
                    'email' => $r->correo,
                    'telefono' => $r->telefono,
                    'direccion' => $r->direccion,
                    'latitud' => $r->latitud,
                    'longitud' => $r->longitud,
                    'ciudad_id' => ( ($r->ciudad_id == 0) ? null : $r->ciudad_id ),
                    'barrio_id' => ( ($r->barrio_id == 0) ? null : $r->barrio_id ),
                    'updated_by' => $r->id,

                );

                if (UtilidadesController::update($query, $data) > 0) {
                    $rta['cod'] = 200;
                    $rta['reg'] = 0;
                    $rta['msg'] = 'OK';
                }
            }
        }

        return response()->json($rta);
    }

    private static function notificarVenta($venta, $importe){
        
        $envio = false;
        try {
            //code...
        
        $correo = 'contacto.corpar@gmail.com';
        $razon = $venta->ruc .' '.$venta->razon_social;
        $html = '';
        $html .= 'Hola <br />';
        $html .= 'Te notificamos una nueva venta concretada desde la app OrietalPy por parte de '.$razon.' por valor de '.$importe.'<br />';
        $html .= 'La modalida de de la venta es '.$venta->modo.' la fecha y hora de entrega solicitada es: '.$venta->entrega_programada.'<br />';
        $html .= 'Por favor ingresa a la web para gestionar el pedido '."<a href='https://oriental.soluciones.dev/login'>Click Aqui</a><br />";
        $html .= 'Que tengas buen resto de jornada<br /><br /><br />';
        $html .= 'OrientalPyApp';

        Mail::send([], [], function ($mail) use ($correo, $html, $razon) {
            $mail
                ->from($correo, 'OrientalPy')
                ->to(env('MAIL_TO'), 'market.orientalpy@gmail.com')
                // ->to(env('MAIL_TO'))
                ->subject('OrientalPY - Nueva venta concretada: '.$razon)
                // ->setBody($html, 'text/html');
                ->html($html);
        });

        
            $envio = true;
            Log::info('Fin del proceso, se envio correo al usuario con su nueva clave exitosamente!');
        
    } catch (\Throwable $th) {
        //throw $th;
        $envio = false;
        Log::error('Ocurrio un error al intentar enviar el correo'.$th->getMessage());
       }

        return $envio;
    }
    private static function notificarCompra($venta, $importe){

        $envio = false;
        try {
               
        $to=DB::table('users')->where('id',$venta->cliente_id)->first();
        $envio = false;
        $correo = 'contacto.corpar@gmail.com';
        $razon = $venta->ruc .' '.$venta->razon_social;
        $html = '';
        $html .= 'Hola <br />';
        $html .= 'Te notificamos una nueva Compra concretada desde la app OrietnalPy por parte de '.$razon.' por valor de '.$importe.'<br />';
        $html .= 'La modalida de de la Compra es '.$venta->modo.' la fecha y hora de entrega solicitada es: '.$venta->entrega_programada.'<br />';
        $html .= 'Que tengas buen resto de jornada<br /><br /><br />';
        $html .= 'OrientalPyApp';

        Mail::send([], [], function ($mail) use ($correo, $html, $razon,$to) {
            $mail
                ->from($correo, 'OrientalPy')
                ->to($to->email ?? '')
                ->subject('OrientalPY - Nueva venta concretada: '.$razon)
                // ->setBody($html, 'text/html');
                ->html($html);
        });

       
        $envio = true;
        Log::info('Fin del proceso, se envio correo al usuario con su nueva clave exitosamente!');
        
    } catch (\Throwable $th) {
        $envio = false;
        Log::error('Ocurrio un error al intentar enviar el correo'.$th->getMessage());
    }

        return $envio;
    }

    public function vender(Request $r)
    {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        Log::info($r);
        $rta['cod'] = 500;
        $rta['msg'] = "Error de Proceso";
        $user = self::getUser($r->header('token'));
        // Log::info($user);

        if (!empty($user->id)) {
            Log::info('user=>' . $user->id);
            Log::info('r=>' . $r->id);
                $validator = Validator::make($r->all(), [
                    'fecha' => 'required',
                    'tipo_entrega' => 'required',
                    'medio_pago' => 'required',
                    'cliente' => 'required',

                ]);
                $cliente = $r->input('cliente');
                if (($cliente == 0) || ($cliente == '0') || ($cliente=="") ) {
                    $cliente = $user->id;
                }
                if ($validator->fails()) {
                    $rta['msg'] = $validator->errors();
                    $rta['cod'] = 422;
                } else {
                    $venta = new venta();
                    $venta->fecha = $r->input('fecha');
                    $venta->canal = 'app';
                    $venta->ruc = $r->input('ruc');
                    $venta->razon_social = $r->input('razon_social');
                    $venta->modo = $r->input('tipo_entrega');
                    $venta->forma_pago = $r->input('medio_pago');
                    $venta->ciudad_id = $r->input('ciudad');
                    $venta->barrio_id = $r->input('barrio');
                    $venta->direccion = $r->input('direccion_entrega');
                    $venta->latitud = $r->input('latitud');
                    $venta->longitud = $r->input('longitud');
                    $venta->estado = 'pendiente';
                    $venta->cliente_id = $user->id;
                    $venta->entrega_programada = $r->input('entrega_programada');
                    $venta->observaciones = $r->input('observaciones');
                    $venta->created_at = now();
                    $venta->created_by = $user->id;
                    $venta->delivery_kilometros = $r->input('delivery_kilometros');
                    $venta->delivery_importe = $r->input('delivery_importe');
                    // $venta->created_by = $r->input('cliente');


                    try {
                        if ($venta->save()) {
                            $detalle = $this->guardarVentaDetalle($venta->id, $r->input('detalles'));
                            //    return response()->json($detalle);
                            if ($detalle['cod'] == 200) {
                                $query = " update ventas set importe=:cond1 where id=:cond2  ";
                                $ve = ApiController::update($query, array('cond1' => $detalle['dta'], 'cond2' => $venta->id));
                                if ($ve > 0) {
                                    self::notificarVenta($venta, $detalle['dta']);
                                    self::notificarCompra($venta, $detalle['dta']);
                                    //procedemos a descontar las ventas
                                    $descontar = $this->descontarStock($r->input('detalles'));
                                    if ($descontar['cod'] == 200) {
                                        $rta['cod'] = 200;
                                        $rta['msg'] = "todo ok/ " . $descontar['msg'];
                                    } else {
                                        $rta['msg'] = $descontar['msg'];
                                    }
                                } else {
                                    $table = "ventas";
                                    $indicador = "venta_id";
                                    $table_detalles = "ventas_detalles";
                                    $de = ApiController::rollback_ventas_detalles($table_detalles, $indicador, $venta->id);
                                    $ve = ApiController::delete($table, $venta->id);
                                    $rta['msg'] = "Error al actualizar la venta";
                                }
                            } else {
                                $rta['msg'] = "Error al insertar los detalles";
                            }

                        }else{
                            $rta['msg'] = "No se pudo registrar la venta";

                        }
                    } catch (\Exception $e) {
                        Log::error(__FUNCTION__ . '=>' . $e->getMessage());
                        $rta['msg'] = "No se pudieron registrar los productos de la venta";
                        // $this->enviar_error();
                        // if (isset($venta->id)) {
                        //     $table = "ventas";
                        //     $ve = ApiController::delete($table, $venta->id);
                        // }
                    }

                }

        } else {
            $rta['cod'] = 418;
            $rta['msg'] = "Su session expiro por favor inicie sesion nuevamente";
        }

        Log::info($rta);


        return $rta;

    }
    public function enviar_error(){
    Log::info(__FUNCTION__.'/'.__FILE__);  $rta['cod']=500; $rta['msg']='Error';
     try {
        // foreach ($correo as  $value) {
        //     Mail::send([], [], function ($mail) use ($value) {
        //         $mail
        //             ->from($value, 'ERROR OrientalPy')
        //             ->to('fnoceda83@gmail.com')
        //             // ->to($correo)
        //             ->subject('Error al realizar venta')
        //             ->setBody('Ocurrio un error al realizar la venta favor revisar urgente', 'text/html');
        //     });

        //     Mail::send([], [], function ($mail) use ($value) {
        //         $mail
        //             ->from($value, 'ERROR OrientalPy')
        //             ->to('alandiaz719@gmail.com')
        //             // ->to($correo)
        //             ->subject('Error al realizar venta')
        //             ->setBody('Ocurrio un error al realizar la venta favor revisar urgente', 'text/html');
        //     });
        // }

     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
    public static function descontarStock($articulos)
    {
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info( $articulos );
        $rta['cod'] = 500;
        $rta['msg'] = 'hubo un error inesperado';
        try {
            foreach ($articulos as $arti) {
                $query = 'select stock_descontar(:id , :cantidad) ';
                // DB::select(DB::raw($query), array('id' => $arti['producto'], 'cantidad' => $arti['cantidad']));
                DB::select(($query), array('id' => $arti['producto'], 'cantidad' => $arti['cantidad']));
                Log::info(DB::getQueryLog());

            }
            $rta['cod'] = 200;
            $rta['msg'] = 'Stock Actualizado';

        } catch (\Throwable $th) {
            Log::info('El query dio error =>' . $th->getMessage());
            $rta['msg'] = 'Fatal error al descontar del stock';
        }
        return $rta;

    }

    public static function guardarVentaDetalle($id, $datos)
    {
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 500;
        $rta['msg'] = 'hubo un error inesperado';
        try {
            $importe_total_ventas = 0;
            foreach ($datos as $venta) {
                $importe_total_ventas = $importe_total_ventas + (($venta['cantidad'] * $venta['precio']));
                $data = array(
                    'venta_id' => $id,
                    'articulo_id' => $venta['producto'],
                    'cantidad' => ($venta['cantidad']),
                    'precio' => ($venta['precio']),
                    'total' => (($venta['cantidad'] * $venta['precio'])),
                    'created_at' => 'now()',
                    'created_by' => 1,
                    'sabor' => $venta['sabor'],
                    'color' => $venta['color'],
                    'medida' => $venta['medida']
                );
                DB::table('ventas_detalles')->insert($data); unset($data);
                Log::info(DB::getQueryLog());
            }

            $rta['cod'] = 200;
            $rta['msg'] = 'Detalles insertados correctamente';
            $rta['dta'] = $importe_total_ventas;

        } catch (\Throwable $th) {
            Log::info('El query dio error =>' . $th->getMessage());
            $rta['msg'] = 'Error al insertar los detalles';

            $table = "ventas";
            $indicador = "venta_id";
            $table_detalles = "ventas_detalles";
            ApiController::rollback_ventas_detalles($table_detalles, $indicador, $id);
            ApiController::delete($table, $id);
        }
        return $rta;
    }

    public function getEmpresas(){
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';
        $query = " select a.id, a.name, a.logo, a.descripcion from empresa a where a.es_activo = true and id in ( select empresa_id from articulos where es_activo = true ) ";
        $data = UtilidadesController::getQuery($query);
        return response()->json($data);
    }

    public function getParametros(){
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';
        $query = " select clave, valor from parametros ";
        $data = UtilidadesController::getQuery($query);
        return response()->json($data);
    }

    public function getValoraciones(){
        Log::info(__FILE__.'/'.__FUNCTION__); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';
        $query = " select v.id, v.comentario, u.name as usuario, v.created_at, articulo_id as articulo, v.valoracion
                    from valoraciones v left join users u on v.created_by = u.id
                    where v.es_publicado = true order by id desc ";
        $rta = UtilidadesController::getQuery($query);
        return response()->json($rta);
    }
    public function setValoraciones(Request $r){
        Log::info(__FILE__.'/'.__FUNCTION__); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';

        $validator = Validator::make($r->all(), [
            'producto' => 'required',
            'valoracion' => 'required'
        ]);

        if ($validator->fails()) {
            Log::info('Fallo la validacion');
            $rta['msg'] = UtilidadesController::arr2str($validator->errors());
            $rta['cod'] = 422;
        } else {
            //si viene el correo buscamos identificamos al usuario
            $query = " select * from users where email = :correo ";
            $data = Array('correo'=>$r->correo);
            $u = UtilidadesController::getFirst($query, $data);
            $query = " insert into valoraciones(comentario, valoracion, articulo_id, created_by) values( :comentario, :valoracion, :articulo_id, :created_by ) ";
            $data = Array('comentario' => $r->comentario, 'valoracion' => $r->valoracion, 'articulo_id' => $r->producto, 'created_by' => ((empty($u['dat']->id))? null: $u['dat']->id));
            if(UtilidadesController::insert($query, $data)){
                $rta['cod'] = 200; $rta['reg'] = 1; $rta['msg'] = 'OK';
                $query = " update articulos set valoracion = (select coalesce(round(avg( valoracion), 2), 0.00) from valoraciones where articulo_id = :articulo) ";
                $data = Array('articulo'=>$r->producto);
                UtilidadesController::update($query, $data);
            }
        }
        return response()->json($rta);
    }

    public function enviarImportacion(Request $r)
    {
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';

        $userMail = str_replace('"', '', $r->correo);
        $userName = str_replace('"', '', $r->name);
        $producto = str_replace('"', '', $r->producto);
        $url = (empty($r->url))? '' : 'Url de ejemplo: <a href="'.str_replace('"', '', $r->url).'">URL</a>';
        $file = $r->file('file');

        $html = ' Hola.! <be /> El sistema de CorPar le notifica que '.$userName.' solicita un presupuesto de '.$producto.$url;
        $html .= ' la direccion de correo del cliente es: '.$userMail;
        try{

            Mail::send([], [], function ($message) use ($html, $userMail, $userName, $producto, $file) {
                $message
                    ->from(env('MAIL_USERNAME'), 'OrientalPY')
                    ->to(env('MAIL_TO'), 'market.orientalpy@gmail.com')
                    ->replyTo($userMail)
                    ->subject('OrientalPYApp - Pedido de Presupuesto de: '.$userName.' producto: '.$producto)
                    ->setBody($html, 'text/html');
                    if(!empty($file)){
                        Log::info('Adjuntando Archivo'); //kaka
                        $message->attach($file, ['as'=>$file->getClientOriginalName()]);
                    }
                });
                
                    $rta['cod'] = 200; $rta['msg'] = "OK";
                    Log::info('Fin del proceso, se envio correo de pedido de presupuesto exitosamente!');
                
        }catch(\Exception $e){
            Log::info('Ocurrio una excepcion en el proceso ');
            Log::error($e->getMessage());
        }

        return response()->json($rta);
    }

    public function articuloImagenDescripcion(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['reg'] = 0; $rta['msg']='Error';
     try {
        DB::enableQueryLog();
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0;
        $rta['reg'] = 0;
        $rta['msg'] = 'OK';
        $query = " select id, articulo_id as articuloId, path as name, orden,descripcion,descripcion_co as descripcionco from articulo_imagen_descripcion where articulo_id = ".$r->articulo." order by orden ";
        $rta = UtilidadesController::getQuery($query);
        return response()->json($rta);
     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
    public function deliveryCoste(Request $r){
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); $rta['cod'] = 500; $rta['reg'] = 0; $rta['msg'] = 'Error de proceso';
         try {
            $query = " select id,kilometros, importe from coste_delivery ";
            $data = UtilidadesController::getQuery($query);
            return response()->json($data);
         } catch (\Throwable $th) {
         Log::error('Error'.$th->getMessage());
         }
         return $rta;
        }

    public function comprasHistorial(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['reg'] = 0; $rta['msg']='Error';
     try {
        DB::enableQueryLog();
        $user = self::getUser($r->header('token'));
        if (!empty($user->id)) {
            Log::info("alan");
        Log::info('user=>' . $user->id);
        $usuario=$user->id;
        // $usuario=85;
        $query = "
            select * from ventas where cliente_id= ".$usuario."  order by id desc

        ";
        // and estado = 'entregado'
        $data = UtilidadesController::getQuery($query);
        Log::info($data);
        foreach ($data['dat'] as $key => $val) {
            $data['dat'][$key]->hijos = self::getVentasDetalles($val->id);
        }

        } else {
            $rta['cod'] = 418;
            $rta['msg'] = "Su session expiro por favor inicie sesion nuevamente";
        }
        return response()->json($data);

     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }

    private function getVentasDetalles ($venta){
    Log::info(__FUNCTION__.'/'.__FILE__);
     try {
        $detalles= DB::table('ventas_detalles as vd')
        ->select('vd.articulo_id','vd.cantidad','a.name')
        ->selectRaw('ROUND(vd.precio) as precio_a')
        ->selectRaw('ROUND(a.precio_venta) as precio_b')
        ->selectRaw('a.por_gramo')
        ->selectRaw('a.unidad_de_medida as unidadmedida')
        ->selectRaw('a.name_co as nameco')
        ->selectRaw(DB::raw('
            (select path from articulo_imagenes where articulo_id = vd.articulo_id  order by orden asc limit 1  ) as path
        '))
        ->join('articulos as a','a.id','=','vd.articulo_id')
        ->join('ventas as v','v.id','=','vd.venta_id')
        ->where('venta_id',$venta)
        ->where('a.es_activo',true)
        ->whereNull('a.deleted_at')
        ->get();
     } catch (\Throwable $th) {
        Log::info($th->getMessage());
        return [];
     }
     return $detalles;
    }

    public function tagsArticulos(Request $r){
        Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['reg'] = 0; $rta['msg']='Error';
         try {
            DB::enableQueryLog();
            $query = "
                select
                --ta.id as ta_id,
                --a.name as articulo,
                --t.name as tag,
                --t.id as tag_id
                a.id as articuloid
                from tags_articulos as ta
                join articulos as a on a.id = ta.articulo_id
                join tags as t on t.id = ta.tag_id
                where tag_id in (
                    select tag_id from tags_articulos where articulo_id= ".$r->articulo."
                )
                and ta.articulo_id <> ".$r->articulo."
                and a.deleted_at is null
                and a.es_activo is true
                order by a.name

            ";
            // and estado = 'entregado'
            $data = UtilidadesController::getQuery($query);
            return response()->json($data);

         } catch (\Throwable $th) {
         Log::error('Error'.$th->getMessage());
         }
         return $rta;
        }


}
