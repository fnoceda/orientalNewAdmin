<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CrudController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileInfo;
use Illuminate\Support\Facades\Auth;

class AbmController extends CrudController
{
    public $r;
    public function __construct(Request $request){
        $this->r = $request;
    }

    public function data()
    {
        $this->setTable($this->r);
        return $this->getDataJson();
    }

    public function index()
    {
        $reg =null;
        $this->setTable($this->r);
        $foo['table'] = $this->table;
        $foo['rute'] = $this->rute;
        $foo['rutedata'] =$this->rute.'data';
        $foo['title'] = $this->title;
        $foo['cols'] = $this->tableColsLables();
        return view($this->ruteName.'.index')->with(['foo'=>$foo, 'reg'=>$reg]);
    }

    public function colorindex(){
        $color = DB::table('colores')->get();
        return view('abms.abmscolor.index')->with(['color'=>$color]);     
    }
    
    public function colorcreate(){
        $color = DB::table('colores')->get();
        return view('abms.abmscolor.create')->with(['color'=>$color]);     
    }
    public function colorguardar(Request $r){
        Log::info(__FUNCTION__);
        $r->validate([
            'name' => 'required',
        ]);
        $data = Array(
            'name'=>$r->name,
            'created_at'=> 'now()',
            'created_by'=> Auth::user()->id,
           );
        try {
            $id = DB::table('colores')->insert($data);
            if ($id > 0) {
                return redirect('/abms/colores/colors/index')->with('status', 'color cargado con exito');
            }
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return back()->withErrors('Hubo un problema en el proceso');
        }
    }
    public function coloredit(Request $r){
        $color = DB::table('colores')->where('id',$r->id)->first();
        return view('abms.abmscolor.edit')->with(['color'=>$color]);     
    }
    public function colorupdate(Request $r){
        Log::info(__FUNCTION__);
        $r->validate([
            'id' => 'required',
            'name' => 'required',
        ]);
        $data = Array(
            'name'=>$r->name,
            'updated_at'=> 'now()',
            'updated_by'=> Auth::user()->id,
           );
        try {
            $id = DB::table('colores')->where('id',$r->id)->update($data);
            if ($id > 0) {
                return redirect('/abms/colores/colors/index')->with('status', 'Color Actualizado con exito');
            }
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return back()->withErrors('Hubo un problema en el proceso');
        }
    }
    public function deletecolor(Request $r){
        $D=DB::table('colores')->where('id', '=', $r->id)->delete();
        if ($D > 0) {
           return back()->with('status', 'Eliminado con exito');
        }else{
            return back()->withErrors('Hubo un problema al querer eliminar');
        }
    }
    public function create()
    {

        $this->setTable($this->r);
        $foo['table'] = $this->table;
        $foo['rute'] = $this->rute;
        $foo['title'] = $this->title;
        $foo['cols'] =  $this->tableCols();

        $reg = NULL;
        // dd( $foo['table']);
       if ($foo['table'] == 'empresa') {
        return view('abms.abmempresa.create')->with(['foo'=>$foo, 'reg'=>$reg]);
       }else{
        return view($this->ruteName.'.create')->with(['foo'=>$foo, 'reg'=>$reg]);
       }

    }


    public function store(Request $request)
    {
        $this->setTable($this->r);
        Log::info($request);
        $this->validate($request,$this->getValidacion());
        if($this->insertar($request->request)){
            $url = explode('/', $request->getPathInfo()); $base = $url[2];
            return redirect()->route('abms', array('base'=>$base) )->with('success','Registro creado satisfactoriamente');
        }else{
            return redirect()->back()->withErrors('Opps Algo salio mal');
        }
    }
    public function guardar(Request $r)
    {
        Log::info(__FUNCTION__);
        $r->validate([
            'name' => 'required'
        ]);
        $stock=false;
        $activo=false;
        $filename = null;
        if(isset($r->stock)){
            $stock =true;
        }
        if(isset($r->es_activo)){
            $activo =true;
        }
        if(!empty($r->logo)){
            $respueta=$this->guardarimagen($r->logo);
            $filename=$respueta['data'];
        }

        $data = Array(
            'name'=>$r->name,
            'logo'=>$filename,
            'stock'=>$stock,
            'ruc'=>$r->ruc,
            'descripcion'=>$r->descripcion,
            'es_activo'=>$activo,
            'created_at'=> 'now()',
            'created_by'=> Auth::user()->id,
           );
        try {
            $id = DB::table('empresa')->insert($data);
            if ($id > 0) {
                return redirect('/abms/empresas')->with('status', 'Empresa cargada con exito');

            }
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return back()->withErrors('Hubo un problema en el proceso');
        }

    }


    public function show($id)
    {
        $this->setTable($this->r);
        $foo['table'] = $this->table;
        $foo['rute'] = $this->rute;
        $foo['title'] = $this->title;
        $foo['cols'] = $this->tableCols();
        $foo['id'] = $this->r->id;
        $registro = $this->getRegistro($this->r->id, 0);
        $reg = (array) $registro[0];
        //dd($reg);
        return view($this->ruteName.'.show')->with(['foo'=>$foo, 'reg'=>$reg]);
    }


    public function edit($id)
    {
        $this->setTable($this->r);
        $foo['table'] = $this->table;
        $foo['rute'] = $this->rute;
        $foo['title'] = $this->title;
        $foo['cols'] = $this->tableCols();
        $foo['id'] =  $this->r->id;
        $registro =$this->getRegistro($this->r->id);
        $reg = (array) $registro[0];
        // dd( $foo['id']);
        if ($foo['table'] == 'empresa') {
            $empresa = DB::table('empresa')->where('id',$foo['id'])->first();
            return view('abms.abmempresa.edit')->with(['foo'=>$foo, 'reg'=>$reg, 'empresa'=>$empresa]);
        }else{
            return view($this->ruteName.'.edit')->with(['foo'=>$foo, 'reg'=>$reg]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->setTable($this->r);
        Log::info($request);
        $camp = $this->getValidacion();
        $this->validate($request, $camp);
        $up = $this->actualizar($request->request);
        if($up > 0){
            $url = explode('/', $request->getPathInfo()); $base = $url[2];
            return redirect()->route('abms', array('base'=>$base) )->with('success','Registro modificado satisfactoriamente');
        }else{
            return redirect()->back()->withErrors('Opps Algo salio mal');
        }
    }

    public function actualizar_empresa(Request $r)
    {
        Log::info(__FUNCTION__); Log::info($r);
        $stock=false;
        $activo=false;
        if(isset($r->stock)){
            $stock =true;
        }
        if(isset($r->es_activo)){
            $activo =true;
        }
        if ($r->logo == null) {
            $r->validate([
                'name' => 'required',
                'ruc' => 'required',
                'descripcion' => 'required',
            ]);
        }else{
            $r->validate([
                'name' => 'required'
            ]);
        }
        $array=[];
        if ($r->logo == null) {
            $array=[
                        'name'=>$r->name,
                        'stock'=>$stock,
                        'ruc'=>$r->ruc,
                        'descripcion'=>$r->descripcion,
                        'es_activo'=>$activo,
                        'updated_at' => 'now()',
                        'updated_by' => Auth::user()->id
            ];
        }else{
            $respueta=$this->guardarimagen($r->logo);
            $filename=$respueta['data'];
             //
             $path=DB::table("empresa")->select('logo')->where("id", '=', $r->id)->first();
             Log::info('eliminar=>'.public_path(). '\storage\empresas\\'.$path->logo);
             $pathToFile = public_path() . '\storage\empresas\\' . $path->logo;
             //  if( file_exists((public_path(). '\storage\empresas\\'.$path->logo)) &&  ){
             if (file_exists($pathToFile) && is_file($pathToFile)) {
                unlink(public_path().'\storage\empresas\\'.$path->logo); //borramos del disco
             }else{
             Log::info('No se econtro');
             }
             //
            $array=[
                        'logo' => $filename,
                        'name'=>$r->name,
                        'stock'=>$stock,
                        'ruc'=>$r->ruc,
                        'descripcion'=>$r->descripcion,
                        'es_activo'=>$activo,
                        'updated_at' => 'now()',
                        'updated_by' => Auth::user()->id
            ];
        }
            try {
                DB::table('empresa')
                ->where("id", '=', $r->id)
                ->update($array);
                 return redirect('/abms/empresas/')->with('status', 'Empresa actualizada con exito');
            } catch (\Throwable $th) {
                Log::info('El query dio error =>'.$th->getMessage());
                return back()->withErrors('Hubo un problema en el proceso');
            }

    }


    public function destroy($id)
    {

          $this->setTable($this->r);
          Log::info($this->r);
          $up =$this->eliminar($this->r->id);
          if (isset($up['cod'])) {
              //significa que es tabla empresa
              if ($up['cod'] == 0) {
                return redirect()->back()->withErrors('La Empresa no puede ser Eliminada porque tiene articulos asociados');
              }else{
                $url = explode('/', $this->r->getPathInfo()); $base = $url[2];
                return redirect()->route('abms', array('base'=>$base) )->with('success','Registro eliminado satisfactoriamente'); 
              }
          }
          Log::info("verga");
          if($up > 0){
              $url = explode('/', $this->r->getPathInfo()); $base = $url[2];
              return redirect()->route('abms', array('base'=>$base) )->with('success','Registro eliminado satisfactoriamente');
          }else{
              return redirect()->back()->withErrors('Opps Algo salio mal');
          }
    }
    public function guardarimagen($dataa){
        Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';

          $patch=public_path('storage/empresas');
          $max_ancho =300;
          $max_alto = 150;
          $min_ancho =250;
          $min_alto = 90;
          $medidasimagen= getimagesize($dataa);
          $medidasimagen= getimagesize($dataa);
          $info = new SplFileInfo($dataa);
          // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
          if ($dataa->extension() == "svg") {
            $filename = time() . "." . $dataa->extension();
            $dataa->move(public_path('storage/empresas'), $filename);
          }
          elseif ($info->getExtension() == "tmp"){
            $filename = time() . "." . $dataa->extension();

            $dataa->move(public_path('storage/empresas'), $filename);

          }elseif($medidasimagen[0] < 500 && $medidasimagen[0] > 100 && $medidasimagen[1] > 100 && $medidasimagen[1] < 500 && (filesize($dataa)) < 100000){
            $filename = time() . "." . $dataa->extension();

            $dataa->move(public_path('storage/empresas'), $filename);

          }else{

          $filename = time() . "." . $dataa->extension();
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

          list($ancho,$alto)=getimagesize($rtOriginal);//alto350 //ancho850
          $x_ratio = $max_ancho / $ancho;//0.70
          $y_ratio = $max_alto / $alto;//0.57

        //   dd($x_ratio,$y_ratio);
          if( ($ancho <= $max_ancho) && ($ancho >= $min_ancho) && ($alto <= $max_alto)  && ($alto >= $min_alto)){
              $ancho_final = $ancho;
              $alto_final = $alto;
          }
          if (($ancho <= $max_ancho) || ($ancho >= $max_ancho)) {
            $ancho_final = 350;
          }
          if (($alto <= $min_alto) || ($alto >= $min_alto)) {
            $alto_final = 100;
          }

          $lienzo=imagecreatetruecolor($ancho_final,$alto_final);

          imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

          //imagedestroy($original);

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


      return $rta;
    }
}
