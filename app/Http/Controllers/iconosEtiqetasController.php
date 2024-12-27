<?php

namespace App\Http\Controllers;

use App\Etiqueta;
use App\Iconos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileInfo;

class iconosEtiqetasController extends Controller
{
    public function index(Request $r){
        if ($r->base =="etiquetas") {
            $data=Etiqueta::orderBy("name")->get();
            $tabla="etiquetas";
        }
        if ($r->base =="iconos") {
            $data=Iconos::orderBy("name")->get();
            $tabla="iconos";
        }
        if (!empty($data)) {
            return view('/admin.iconos_etiquetas.index',['data' => $data,'tabla' => $tabla]);
        }else{
            return redirect()->action('HomeController@index');
        }  
    }
    public function edit(Request $r){
        if ($r->tabla =="etiquetas") {
            $data=Etiqueta::where("id",$r->id)->first();
            $tabla="etiquetas";
        }
        if ($r->tabla =="iconos") {
            $data=Iconos::where("id",$r->id)->first();
            $tabla="iconos";
        }
        if (!empty($data)) {
            return view('/admin.iconos_etiquetas.edit',['tabla' => $tabla,'id' => $r->id,'data' => $data]);
        } 
       

    }
    public function updated(Request $r){
        Log::info(__FUNCTION__);
        if ($r->path == null) {
            if ($r->tabla_name == "etiquetas") {
                $r->validate([
                    'name' => 'required',
                    'porcentaje_descuento' => 'required|numeric',
                    'tabla_name' => 'required',
                ]);
                
            }else{
                $r->validate([
                    'name' => 'required',
                    'tabla_name' => 'required',
                ]);
            }
           
        } else {
            if ($r->tabla_name == "etiquetas") {
                $r->validate([
                    'path' => 'required|mimes:jpeg,bmp,png,svg',
                    'name' => 'required',
                    'porcentaje_descuento' => 'required|numeric',
                    'tabla_name' => 'required',
                ]);
                
            }else{
                $r->validate([
                    'path' => 'required|mimes:jpeg,bmp,png,svg',
                    'name' => 'required',
                    'tabla_name' => 'required',
                ]);
            }
            
        }
        

        if ($r->tabla_name == "iconos") {
            $array=[];
            if ($r->path == null) {
                $array=[
                'name' => $r->name,  
                'updated_at' => 'now()', 
                'updated_by' => auth()->user()->id
                ];
            }else{
            $respueta=$this->guardarimagen($r->path);
            $filename=$respueta['data'];
            //
            $path=DB::table($r->tabla_name)->select('path')->where("id", '=', $r->id)->first();
            Log::info('eliminar=>'.public_path(). '\storage\img\\'.$path->path);
            if( file_exists((public_path(). '\storage\img\\'.$path->path)) ){
            unlink(public_path().'\storage\img\\'.$path->path); //borramos del disco
            }else{
            Log::info('No se econtro');
            }
            //
                $array=[
                    'name' => $r->name,
                    'path' => $filename,  
                    'updated_at' => 'now()', 
                    'updated_by' => auth()->user()->id
                    ];
            }
            try {
                DB::table($r->tabla_name)
                ->where("id", '=', $r->id)
                ->update($array);
                 return redirect('/admin/images/'.($r->tabla_name))->with('status', 'Icono cargado con exito');
            } catch (\Throwable $th) {
                Log::info('El query dio error =>'.$th->getMessage());
                return back()->withErrors('Hubo un problema en el proceso');
            }
        }elseif ($r->tabla_name == "etiquetas"){
                $array=[];
            if ($r->path == null) {
                // dd("sin imagen");
                $array=[
                'name' => $r->name, 
                'porcentaje_descuento' => $r->porcentaje_descuento,  
                'updated_at' => 'now()', 
                'updated_by' => auth()->user()->id
                ];
            }else{
                $respueta=$this->guardarimagen($r->path);
                $filename=$respueta['data'];
                //
                $path=DB::table($r->tabla_name)->select('path')->where("id", '=', $r->id)->first();
                Log::info('eliminar=>'.public_path(). '\storage\img\\'.$path->path);
                if( file_exists((public_path(). '\storage\img\\'.$path->path)) ){
                unlink(public_path().'\storage\img\\'.$path->path); //borramos del disco
                }else{
                Log::info('No se econtro');
                }
                //
                $array=[
                    'name' => $r->name,
                    'path' => $filename, 
                    'porcentaje_descuento' => $r->porcentaje_descuento,  
                    'updated_at' => 'now()', 
                    'updated_by' => auth()->user()->id
                    ];
                }
                try {
                    DB::table($r->tabla_name)
                    ->where("id", '=', $r->id)
                    ->update($array);
                    Log::info(DB::getQueryLog());
                    return redirect('/admin/images/'.($r->tabla_name))->with('status', 'Etiqueta cargado con exito');
                } catch (\Throwable $th) {
                    Log::info('El query dio error =>'.$th->getMessage());
                    return back()->withErrors('Hubo un problema en el proceso');
                }
        }else{
            return back()->withErrors( 'Hubo un problema en el proceso');
        }

    }
    public function delete(Request $r){
        $D=DB::table($r->tabla)->where('id', '=', $r->id)->delete();
        if ($D > 0) {
           return back()->with('status', 'Eliminado con exito');
        }else{
            return back()->withErrors('Hubo un problema al querer eliminar');
        }
    }
    public function create(Request $r){
        Log::info(__FUNCTION__);

        return view('/admin.iconos_etiquetas.create',['tabla' => $r->tabla]);
    }
    public function guardar(Request $r){
        Log::info(__FUNCTION__); Log::info($r);
        try {
            if ($r->tabla_name == "etiquetas") {
                $r->validate([
                    'path' => 'required|mimes:jpeg,bmp,png,svg',
                    'name' => 'required',
                    'porcentaje_descuento' => 'required|numeric',
                    'tabla_name' => 'required',
                ]);
                
            }else{
                $r->validate([
                    'path' => 'required|mimes:jpeg,bmp,png,svg',
                    'name' => 'required',
                    'tabla_name' => 'required',
                ]);
            }
           
    
            // $filename = time() . "." . $r->path->extension();
            Log::info($r->path->extension());
            if ($r->tabla_name == "iconos") {
                // $r->path->move(public_path('storage/img'), $filename);
                    $respueta=$this->guardarimagen($r->path);
                    $filename=$respueta['data'];
                try {
                    Iconos::create(['name' => $r->name,'path' => $filename, 'created_by' => auth()->user()->id,'created_at' =>'now()']);
                    return redirect('/admin/images/'.($r->tabla_name))->with('status', 'Icono cargado con exito');
                    // return back()->with('status', 'Icono cargado con exito');
                } catch (\Throwable $th) {
                    Log::info('El query dio error =>'.$th->getMessage());
                    return back()->withErrors('Hubo un problema en el proceso');
                }
            }elseif ($r->tabla_name == "etiquetas"){
                
                    // $r->path->move(public_path('storage/img'), $filename);
                    $respueta=$this->guardarimagen($r->path);
                    $filename=$respueta['data'];
                    try {
                        Etiqueta::create(['name' => $r->name,'porcentaje_descuento' => $r->porcentaje_descuento,'path' => $filename, 'created_by' => auth()->user()->id,'created_at' =>'now()']);
                        return redirect('/admin/images/'.($r->tabla_name))->with('status', 'Icono cargado con exito');
                        // return back()->with('status', 'Etiqueta cargada con exito');
                    } catch (\Throwable $th) {
                        Log::info('El query dio error =>'.$th->getMessage());
                        return back()->withErrors('Hubo un problema en el proceso');
                    }
            }else{
                return back()->withErrors( 'Hubo un problema en el proceso');
            }
            
        } catch (\Throwable $th) {
            Log::info("error".$th->getMessage());
            return back()->withErrors( 'Hubo un problema en el proceso');
        }
        
    }







    public function guardarimagen($dataa){
        Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';
        try {
            $patch=public_path('storage/img');
          $max_ancho = 40;
          $max_alto = 40;
          $min_ancho =28;
          $min_alto = 28;
          Log::info($dataa);
          $medidasimagen= getimagesize($dataa);
          $info = new SplFileInfo($dataa);
          #preguntamos si no es un svg y guardamos directo
                    // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual

          if ($dataa->extension() == "svg") {
            $filename = time() . "." . $dataa->extension();
            $dataa->move(public_path('storage/img'), $filename);
          }
          elseif ($info->getExtension() == "tmp"){
            $filename = time() . "." . $dataa->extension();
            $dataa->move(public_path('storage/img'), $filename);
          }elseif($medidasimagen[0] < 40 && $medidasimagen[1] < 40 && (filesize($dataa)) < 100000){
            $filename = time() . "." . $dataa->extension();    
            $dataa->move(public_path('storage/img'), $filename);
          }else{
          $filename = time() . "." . $dataa->extension();
          // Redimensionar
          // dd($dataa->getPathName());
          $rtOriginal=$dataa->getPathName();
    
          if($dataa->getClientMimeType() =='image/jpeg'){
          $original = imagecreatefromjpeg($rtOriginal);
          }
          else if($dataa->getClientMimeType() =='image/png'){
          $original = imagecreatefrompng($rtOriginal);
          }
          else if($dataa->getClientMimeType() =='image/gif'){
          $original = imagecreatefromgif($rtOriginal);
          }
          
          list($ancho,$alto)=getimagesize($rtOriginal);
          $x_ratio = $max_ancho / $ancho;
          $y_ratio = $max_alto / $alto;
    
        if( ($ancho <= $max_ancho) && ($ancho >= $min_ancho) && ($alto <= $max_alto)  && ($alto >= $min_alto)){
            $ancho_final = $ancho;
            $alto_final = $alto;
        }
        if (($ancho <= $max_ancho) || ($ancho >= $max_ancho)) {
          $ancho_final = 26;
        }
        if (($alto <= $min_alto) || ($alto >= $min_alto)) {
          $alto_final = 26;
        }
    
          $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 
    
          imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
          
        $cal=8;
        if($dataa->getClientMimeType()=='image/jpeg'){
            imagejpeg($lienzo,$patch."/".$filename);
            }
            else if($dataa->getClientMimeType()=='image/png'){
            imagepng($lienzo,$patch."/".$filename);
            }
            else if($dataa->getClientMimeType()=='image/gif'){
            imagegif($lienzo,$patch."/".$filename);
            }
    
        }
        if (isset($filename) && !empty($filename)) {
            $rta['cod']=200;
            $rta['data']=$filename;
        }else{
          $rta['msg'] = 'error no se pudo cargar la imagen';
        }
        
        } catch (\Throwable $th) {
           Log::error("error".$th->getMessage());
        }
          
      return $rta;
    }
    
}
