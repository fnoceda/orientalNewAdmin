<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class UtilidadesController extends Controller{

    public static function selected($table){
        try {
          $data = DB::table($table)
            ->whereNull('deleted_at')
            ->get();
          $output ="";
          if ($table == "colores") {
            foreach($data as $row)
            {
             $output .='<div class="modal-body"> <div class="form-check"><input name='.$row->name.' class="form-check-input" type="checkbox"  id='.substr($row->name,1).'><label class="form-check-label mt-0 mb-0" for="defaultCheck1" style="background: '.$row->name.'">'.ucwords($row->name).'</label></div></div>';
            }
          }else{
            foreach($data as $row)
            {
             $output .= '<option value="'.$row->name.'" class="nav-link">'.ucwords($row->name).'</option>';
            }
          }
          
    
          } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
          }
          return $output;
    
      }
    static function strContains($cadena, $subcadena){
        //retorna true si cadena tiene adentro subcadena util para validar errores por unique
        return strpos($cadena, $subcadena) !== false;
    }
      
    public static function claveAleatoria($min=3, $max=3){
        $nombre = '';
        $vocales = Array('a', 'e', 'i', 'o', 'u');
        $consonantes = Array(b'', 'c', 'd', 'f', 'g', 'j', 'l', 'm', 'n', 'p', 'r', 's', 't');
        $random_nombre = rand($min, $max); //largo de la palabra
        $random = rand(0,1); //si empieza por vocal o consonante
        for($j=0;$j<$random_nombre;$j++){ //palabra
                switch($random){
                        case 0: $random_vocales = rand(0, count($vocales)-1); $nombre.= $vocales[$random_vocales]; $random = 1; break;
                        case 1: $random_consonantes = rand(0, count($consonantes)-1); $nombre.= $consonantes[$random_consonantes]; $random = 0; break;
                }
        }
        $nombre .= random_int(0, 9).random_int(0, 9).random_int(0, 9);
        return $nombre;
    }


    public static function arr2str($foo){ //convierte array a cadena con saltos de linea HTML de ser requerido

        $array = (is_array($foo))? $foo : json_decode(json_encode($foo), true) ; // si es un objeto convertimos a array

        $str = '';
        foreach($array as $key=>$val){
            if(is_array($val)){
                $str .= self::arr2str($val);
            }else{
                if(is_numeric($key)){
                    $str .= (empty($str))? ucwords(strtolower($val)).' ' : '.'.ucwords(strtolower($val)).' ';
                }else{
                    $str .= (empty($str))? ucwords(strtolower($key)).': '.ucwords(strtolower($val)).' ' : '.'.ucwords(strtolower($key)).': '.ucwords(strtolower($val)).' ';
                }
            }
        }
        return $str;
    }
    public static function getQuery($query, $parametros = NULL){
        // Esta función procesa las consultas del tipo RAW a la base de datos,
        // Retorna un array de objetos
        DB::enableQueryLog(); // Log::info(__FILE__.'/'.__FUNCTION__);

        try {
            // Validar que el query sea un string
            if (!is_string($query)) {
                throw new \Exception('El parámetro $query debe ser una cadena SQL válida.');
            }

            if (empty($parametros)) {
                $data = DB::select(DB::raw($query));
                if (!env('APP_DEBUG')) {
                    Log::info($query);
                }
            } else {
                if (!env('APP_DEBUG')) {
                    Log::info($query);
                    foreach ($parametros as $key => $val) {
                        Log::info($key . ' => ' . $val);
                    }
                }
                $data = DB::select(DB::raw($query), $parametros); // ✅ solo una ejecución
            }

            $rta['cod'] = 200;
            $rta['msg'] = 'OK';
            $rta['reg'] = count($data);
            $rta['dat'] = $data;

        } catch(\Illuminate\Database\QueryException $e) {
            $rta['cod'] = 500;
            $rta['msg'] = 'Ocurrió un error al realizar la consulta';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());

        } catch (\Throwable $e) {
            $rta['cod'] = 500;
            $rta['msg'] = 'Ocurrió un error fatal al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());

        } catch (\Exception $e) {
            $rta['cod'] = 500;
            $rta['msg'] = 'Ocurrió un error al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());
        }

        return $rta;
    }

    // public static function getQuery($query, $parametros = NULL){
    //     //esta funcion procesa las consultas del tipo RAW a la base de datos,
    //     //retorna un array de objetos
    //     DB::enableQueryLog(); //Log::info(__FILE__.'/'.__FUNCTION__);

    //     try{
    //         if(empty($parametros)){
    //             $data = DB::select( DB::raw($query));
    //             if(!env('APP_DEBUG')) { Log::info($query); }
    //         }else{
    //             if(!env('APP_DEBUG')) { Log::info($query); foreach($parametros as $key=>$val){ Log::info($key.'=>'.$val); }}
    //             $data = DB::select( DB::raw($query));
    //             $data = DB::select( DB::raw($query), $parametros );
    //         }
    //         $rta['cod'] = 200;
    //         $rta['msg'] = 'OK';
    //         $rta['reg'] = count($data);
    //         $rta['dat'] = $data;
    //     } catch(\Illuminate\Database\QueryException $e){
    //         $rta['cod'] = 500;
    //         $rta['msg'] = 'Ocurrio un error al realizar la consulta';
    //         $rta['reg'] = 0;
    //         $rta['dat'] = NULL;
    //         Log::error($rta['msg'].' => '.$e->getMessage());
    //     }catch(Throwable $e){
    //         Log::error($e->getMessage());
    //         $rta['cod'] = 500;
    //         $rta['msg'] = 'Ocurrio un error fatal al conectar a la base de datos';
    //         $rta['reg'] = 0;
    //         $rta['dat'] = NULL;
    //         Log::error($rta['msg'].' => '.$e->getMessage());
    //     }catch (Exception $e) {
    //         $rta['cod'] = 500;
    //         $rta['msg'] = 'Ocurrio un error al conectar a la base de datos';
    //         $rta['reg'] = 0;
    //         $rta['dat'] = NULL;
    //         Log::error($rta['msg'].' => '.$e->getMessage());
    //     }

    //     return $rta;
    // }

    public static function getFirst($query, $parametros = NULL){
        //esta funcion retorna un bojeto solo con el primer registro
        DB::enableQueryLog(); //Log::info(__FILE__.'/'.__FUNCTION__);
        $rta['cod'] = 0; $rta['msg'] = 'Error de proceso';

        try{
            if(empty($parametros)){
                $data = DB::select( DB::raw($query));
            }else{
                // Log::info('hay parametros'); foreach($parametros as $key=>$val){ Log::info($key.'=>'.$val); }
                $data = DB::select( DB::raw($query), $parametros );
            }
            Log::info(DB::getQueryLog());
            $rta['cod'] = 1;
            $rta['msg'] = 'OK';
            $rta['reg'] = count($data);
            if(!empty($data)){
                $rta['dat'] = $data[0];
            }else{
                $rta['dat'] = null;
            }
        }catch(\Illuminate\Database\QueryException $e){
            $rta['cod'] = 0;
            $rta['msg'] = '1 Ocurrio un error al realizar la consulta';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());
        }catch(Throwable $e){
            $rta['cod'] = 0;
            $rta['msg'] = '3 Ocurrio un error fatal al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());
        }catch (Exception $e) {
            $rta['cod'] = 0;
            $rta['msg'] = '4 Ocurrio un error al conectar a la base de datos';
            $rta['reg'] = 0;
            $rta['dat'] = NULL;
            Log::error($rta['msg'].' => '.$e->getMessage());
        }

        return $rta;
    }

    public static function update($query, $data){
        //esta funcion actualiza registros y retorna el numero de registros actualizados
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); $rta = 0;

        try{
            Log::info($query); foreach($data as $key=>$val){ Log::info($key.'=>'.$val); }
            $rta = DB::update($query, $data);
            Log::info(DB::getQueryLog());
        }catch(\Illuminate\Database\QueryException $e){
            Log::error('QueryException => '.$e->getMessage());
        }catch(Throwable $e){
            Log::error('Throwable => '.$e->getMessage());
        }catch (Exception $e) {
            Log::error('Exception => '.$e->getMessage());
        }

        return $rta;
    }

    public static function insert($query, $data){
        //Inserta una registro y retorna true o false
        DB::enableQueryLog(); Log::info(__FILE__.'/'.__FUNCTION__); $rta = false;

        try{
            Log::info($query); foreach($data as $key=>$val){ Log::info($key.'=>'.$val); }

            if(DB::insert($query, $data)){
                $rta = true; Log::info(DB::getQueryLog());
            }else{
                Log::info('Insert no dio error pero retorna false');
            }
        }catch(\Illuminate\Database\QueryException $e){
            Log::error('QueryException => '.$e->getMessage());
        }catch(Throwable $e){
            Log::error('Throwable => '.$e->getMessage());
        }catch (Exception $e) {
            Log::error('Exception => '.$e->getMessage());
        }

        return $rta;
    }

}
