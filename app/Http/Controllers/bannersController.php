<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileInfo;

class bannersController extends Controller
{
    public function index(Request $r){
        Log::info(__FUNCTION__); Log::info($r);


        $data = DB::table('banners')
                    ->join('secciones', 'secciones.id', '=', 'banners.seccion_id')
                    ->select(
                    'banners.id',
                    'banners.name',
                    'banners.es_activo',
                    'banners.destino',
                    'banners.path',
                    'banners.parametro')
                    ->selectRaw('( case when secciones.id = 1 then (select name from categorias where id = banners.parametro::integer) else (select name from articulos where id = banners.parametro::integer) end ) as elemento')
                    ->selectRaw('secciones.name as seccion')
                    ->get();
        if (!empty($data)) {
            return view('/admin.banners.index',['data' => $data]);
        }else{
            return redirect()->action('HomeController@index');
        }
    }
    public function create(Request $r){
        Log::info(__FUNCTION__);
        $secciones = DB::table('secciones')->get();
        $categorias = DB::table('categorias')->where('activo', '=', 'true')->get();
        $articulos = DB::table('articulos')->get();
        $selectdoscategorias= ArticulosController::selected();
        $selectdosarticulos= bannersController::selected();
        $promos = Array();






        return view('/admin.banners.create',['selectdoscategorias' => $selectdoscategorias, 'selectdosarticulos' => $selectdosarticulos, 'secciones' => $secciones, 'categorias' => $categorias, 'articulos' => $articulos, 'promos' => $promos]);
    }
    public static function selected(){
        try {
    
          $data = DB::table('articulos')
            ->whereNull('deleted_at')
            ->get();
          $output ="";
          foreach($data as $row)
          {
           $output .= '<option value="'.$row->id.'" class="nav-link">'.ucwords($row->name).'</option>';
    
          }
    
          } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
          }
          return $output;
    
      }
    //

    public function guardar(Request $r){
        Log::info(__FUNCTION__);
        $r->validate([
            'path' => 'required|mimes:jpeg,bmp,png,svg',
            'name' => 'required',
            'parametro' => 'required',
            'seccion_id' => 'required',
            'destino' => 'required',

        ]);
        $activo=false;
        if (isset($r->es_activo)) {
            $activo=true;
        }
        // $filename = time() . "." . $r->path->extension();

        // $r->path->move(public_path('storage/banners/'), $filename);
        $respueta=$this->guardarimagen($r->path);
        $filename=$respueta['data'];
        $data = Array(
            'name'=>$r->name,
            'path'=>$filename,
            'seccion_id'=>$r->seccion_id,
            'destino'=>$r->destino,
            'es_activo'=>$activo,
            'parametro'=>$r->parametro,
            'created_at'=> 'now()',
            'created_by'=> Auth::user()->id,
           );
        try {
            $id = DB::table('banners')->insert($data);
            if ($id > 0) {
                return redirect('/banners/images/')->with('status', 'Banner cargado con exito');

            }
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return back()->withErrors('Hubo un problema en el proceso');
        }


    }
    public function delete(Request $r){
        $D=DB::table('banners')->where('id', '=', $r->id)->delete();
        if ($D > 0) {
           return back()->with('status', 'Eliminado con exito');
        }else{
            return back()->withErrors('Hubo un problema al querer eliminar');
        }
    }
    public function edit(Request $r){
        $data=
        DB::table('banners')
        ->join('secciones', 'secciones.id', '=', 'banners.seccion_id')
        ->select(
        'banners.id',
        'banners.es_activo',
        'banners.name',
        'banners.path',
        'banners.destino',
        'banners.parametro')
        ->selectRaw('secciones.name as seccion')
        ->selectRaw('secciones.id as seccion_id')
        ->selectRaw('secciones.page as Page')
        ->where('banners.id', $r->id)
        ->first();


        $secciones = DB::table('secciones')->get();
        $categorias = DB::table('categorias')->where('activo', '=', 'true')->get();
        $articulos = DB::table('articulos')->get();
        $promos = Array();


        // dd($data);
        return view('/admin.banners.edit',['id' => $r->id, 'data' => $data, 'secciones' => $secciones, 'categorias' => $categorias, 'articulos' => $articulos, 'promos' => $promos]);

    }
    public function updated(Request $r){
        Log::info(__FUNCTION__);
        if ($r->path == null) {
            $r->validate([
                'name' => 'required',
                'parametro' => 'required',
                'seccion_id' => 'required',
                'destino' => 'required',
            ]);
        }else{
            $r->validate([
                'path' => 'required|mimes:jpeg,bmp,png,svg',
                'name' => 'required',
                'parametro' => 'required',
                'seccion_id' => 'required',
                'destino' => 'required',
            ]);
        }
        $activo=false;
        if (isset($r->es_activo)) {
            $activo=true;
        }
        $array=[];
        if ($r->path == null) {
            $array=[
                        'name' => $r->name,
                        'seccion_id' => $r->seccion_id,
                        'destino'=>$r->destino,
                        'parametro' => $r->parametro,
                        'es_activo'=>$activo,
                        'updated_at' => 'now()',
                        'updated_by' => Auth::user()->id
            ];
        }else{
            $respueta=$this->guardarimagen($r->path);
            $filename=$respueta['data'];
             //
             $path=DB::table("banners")->select('path')->where("id", '=', $r->id)->first();
             Log::info('eliminar=>'.public_path(). '\storage\banners\\'.$path->path);
             if( file_exists((public_path(). '\storage\banners\\'.$path->path)) ){
             unlink(public_path().'\storage\banners\\'.$path->path); //borramos del disco
             }else{
             Log::info('No se econtro');
             }
             //
            $array=[
                        'name' => $r->name,
                        'path' => $filename,
                        'seccion_id' => $r->seccion_id,
                        'destino'=>$r->destino,
                        'es_activo'=>$activo,
                        'parametro' => $r->parametro,
                        'updated_at' => 'now()',
                        'updated_by' => Auth::user()->id
            ];
        }
            try {
                DB::table('banners')
                ->where("id", '=', $r->id)
                ->update($array);
                 return redirect('/banners/images/')->with('status', 'banner actualizado con exito');
            } catch (\Throwable $th) {
                Log::info('El query dio error =>'.$th->getMessage());
                return back()->withErrors('Hubo un problema en el proceso');
            }

    }




    public function guardarimagen($dataa){
        Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';
        
          $patch=public_path('storage/banners');
          $max_ancho =300;
          $max_alto = 150;
          $min_ancho =250;
          $min_alto = 90;
          $medidasimagen= getimagesize($dataa);
          $medidasimagen= getimagesize($dataa);
          $info = new SplFileInfo($dataa);
          // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
        //   if ($info->getExtension() == "tmp"){
            $filename = time() . "." . $dataa->extension();
    
            $dataa->move(public_path('storage/banners'), $filename);

        //   }elseif($medidasimagen[0] < 400 && $medidasimagen[0] > 300 && $medidasimagen[1] > 90 && $medidasimagen[1] < 150 && (filesize($dataa)) < 100000){
        //     $filename = time() . "." . $dataa->extension();
    
        //     $dataa->move(public_path('storage/banners'), $filename);
            
        //   }else{
    
        //   $filename = time() . "." . $dataa->extension();
        //   $rtOriginal=$dataa->getPathName();
    
        //   if($dataa->getClientMimeType() =='image/jpeg'){
        //   $original = imagecreatefromjpeg($rtOriginal);
        //   }
        //   else if($dataa->getClientMimeType() =='image/png'){
        //   $original = imagecreatefrompng($rtOriginal);
        //   }
        //   else if($dataa->getClientMimeType() =='image/gif'){
        //   $original = imagecreatefromgif($rtOriginal);
        //   }
          
        //   list($ancho,$alto)=getimagesize($rtOriginal);//alto350 //ancho850
        //   $x_ratio = $max_ancho / $ancho;//0.70
        //   $y_ratio = $max_alto / $alto;//0.57
    
        // //   dd($x_ratio,$y_ratio);
        //   if( ($ancho <= $max_ancho) && ($ancho >= $min_ancho) && ($alto <= $max_alto)  && ($alto >= $min_alto)){
        //       $ancho_final = $ancho;
        //       $alto_final = $alto;
        //   }
        //   if (($ancho <= $max_ancho) || ($ancho >= $max_ancho)) {
        //     $ancho_final = 350;
        //   }
        //   if (($alto <= $min_alto) || ($alto >= $min_alto)) {
        //     $alto_final = 100;
        //   }
    
        //   $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 
    
        //   imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
          
        //   //imagedestroy($original);
     
        // $cal=8;
    
        // if($dataa->getClientMimeType()=='image/jpeg'){
        // imagejpeg($lienzo,$patch."/".$filename);
        // }
        // else if($dataa->getClientMimeType()=='image/png'){
        // imagepng($lienzo,$patch."/".$filename);
        // }
        // else if($dataa->getClientMimeType()=='image/gif'){
        // imagegif($lienzo,$patch."/".$filename);
        // }
    
        // }
        if (isset($filename) && !empty($filename)) {
            $rta['cod']=200;
            $rta['data']=$filename;
        }else{
          $rta['msg'] = 'error no se pudo cargar la imagen';
        }
        
      
      return $rta;
    }
}
