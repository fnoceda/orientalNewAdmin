<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Iconos;
use App\Categorias;
use App\Articulos;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Storage;
use Whoops\Run;
use SplFileInfo;


class ArticulosController extends Controller
{

  public function articulos(){
    $categorias = Categorias::All();
    $categorias_anidadas= $this->selected();
    $sabores= UtilidadesController::selected('sabores');
    $colores= UtilidadesController::selected('colores');
    $medidas= UtilidadesController::selected('medidas');
    $colores_completos=DB::table('colores')->get();
    $iconos = Iconos::All();
    $articulos =Articulos::All();
    $listas = DB::table('listas_precios')->get();
    $etiquetas = DB::table('etiquetas')->get();
    $articulo_lista = DB::table('articulos_listas_precios')->get();
    $articulo_imagenes = DB::table('articulo_imagenes')->orderBy('id','asc')->orderBy('orden','asc')->get();
    if( (Auth::user()->perfil_id == 1) && (Auth::user()->empresa_id == null)  ){
      $empresa = DB::table('empresa')->whereNull('deleted_at')->orderBy('id', 'asc')->get();
    }else{
      $empresa = DB::table('empresa')->where('id',Auth::user()->empresa_id )->whereNull('deleted_at')->orderBy('id', 'asc')->get();
    }
    if(Auth::user()->perfil_id > 2){
      $usuarios = DB::table('users')->whereNull('deleted_at')->whereRaw('perfil_id > 3')->get();
    }else{
      $usuarios = DB::table('users')->whereNull('deleted_at')->where('perfil','admin')->get();
    }
    $plazo=DB::table('plazo_entrega')->whereNull('deleted_at')->get();
    return view('admin.articulos.index_articulo', compact('plazo','usuarios','articulos', 'categorias', 'iconos','listas','etiquetas','articulo_lista','articulo_imagenes','empresa','categorias_anidadas','sabores','medidas','colores','colores_completos'));
  }

  public function fil($r){
    $articulos_datatable="";
    try {
      $p = $r->categoria;
      $d = $r->empresa;
      $c = $r->usuario;
      $articulos_datatable = DB::table('articulos')
      ->join('categorias','categorias.id','=','articulos.categoria_id')
      ->leftJoin('users as uc','uc.id','=','articulos.created_by')
      ->leftJoin('users as uu','uu.id','=','articulos.updated_by')
      ->select('articulos.id','articulos.name','articulos.name_co',
      'articulos.codigo','articulos.precio_venta','articulos.existencia',
      'uc.name as creadoPor',
      'articulos.unidad_de_medida',
      'uu.name as actualizadoPor'
      )
      ->selectRaw('categorias.name as categoria')
      ->when($p, function($query, $p){
        return $query->where('categoria_id', '=', $p);
    })
    ->when($d, function($query, $d){
        return $query->where('articulos.empresa_id', '=', $d);
    })
    ->when($c, function($query, $c){
      return 
        $query->where('articulos.created_by', '=', $c)
        ->orWhere('articulos.updated_by', '=', $c);
  })
      // ->where('articulos.empresa_id', '=', $r->empresa)
      // ->where('articulos.categoria_id', '=', $r->categoria)
    ;
    return $articulos_datatable;
    } catch (\Throwable $th) {
      Log::error("erro".$th->getMessage());
    }
    return $articulos_datatable;
  }
  public function datatable(Request $r)
    {
        Log::info(__FILE__.'/'.__FUNCTION__);
        $rs = $this->fil($r);
        return DataTables::of($rs)
          ->addColumn('acciones', function ($rs) {
              $botones="";
              $botones .= '<button onclick="editame(\'' .$rs->id. '\')" class="btn btn-primary btn-sm ml-1 mr-1 mt-1 mb-1" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-edit"></i></button>';
              $botones .= '<button onclick="descripcion( \''.route('articulo.descripcion',['id' => $rs->id]).'\'  )" class="btn btn-primary btn-sm ml-1 mr-1 mt-1 mb-1" data-toggle="tooltip" data-placement="top" title="imagenes y Descripcion"><i class="far fa-image"></i></button>';
              $botones .='<button class="btn btn-danger btn-sm" style="border-radius: 50px; ml-1 mr-1 mt-1 mb-1" title="Eliminar" onclick=" modalOpen( \''.$rs->id.'\');"><i class="fas fa-trash"  type="button" ></i></button>';
              return $botones;
          })
          ->editColumn('id', '{!!$id!!}')
          ->rawColumns(['attachment', 'acciones'])
          ->make(true);
    }


  public function filtro_categorias(Request $r){
    if ($r->filtar == 0 ) {
      return Redirect::to('/articulos/listar');
    }else{
      $categorias = Categorias::All();
      $categorias_anidadas= $this->selected();
      $iconos = Iconos::All();
      $articulos =Articulos::All();
      $sabores= UtilidadesController::selected('sabores');
      $colores= UtilidadesController::selected('colores');
      $medidas= UtilidadesController::selected('medidas');
      $colores_completos=DB::table('colores')->get();
      $listas = DB::table('listas_precios')->get();
      $etiquetas = DB::table('etiquetas')->get();
      $articulo_lista = DB::table('articulos_listas_precios')->get();
      $articulo_imagenes = DB::table('articulo_imagenes')->orderBy('id','asc')->orderBy('orden','asc')->get();
      $empresa = DB::table('empresa')->whereNull('deleted_at')->orderBy('id', 'asc')->get();
      $articulos_datatable = DB::table('articulos')->
      join('categorias','categorias.id','=','articulos.categoria_id')
      ->select('articulos.id','articulos.name','articulos.name_co','articulos.codigo','articulos.precio_venta','articulos.existencia')
      ->selectRaw('categorias.name as categoria')
      ->where('articulos.categoria_id', $r->filtar)
      ->where(function($q) use($r){
        if(!empty( $r->condicion )){
            if($r->filtar_empresa == 0){
                $q->whereNotNull('articulos.name');
            }else{
                $q->where('articulos.empresa_id', '=', $r->filtar_empresa);
            }
        }
      })
      ->get();
      // dd($articulos_datatable);
      return view('/admin.articulos.index_articulo', compact('articulos', 'categorias', 'iconos','listas','etiquetas','articulo_lista','articulo_imagenes','empresa','articulos_datatable','categorias_anidadas','sabores','medidas','colores','colores_completos'));
      }
    }

  //
  public static function selected(){
    try {

      $data = DB::table('categorias')
        ->where('activo',true)
        ->whereNull('padre')
        ->whereNull('deleted_at')
        // ->where('entidad_id',Auth::user()->entidad_id)
        ->get();
      $output ="";
      foreach($data as $row)
      {
       $output .= '<option value="'.$row->id.'" class="nav-link">'.ucwords($row->name).'</option>';

       $output .= ArticulosController::heredada($row->id,$row->name);

      }

      } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
      }
      return $output;

  }
  public static function heredada($id,$name){
    // Log::info(__FUNCTION__);
    $data = DB::table('categorias')
      ->where('padre',  $id)
      ->where('activo',true)
      ->whereNull('deleted_at')
      // ->where('entidad_id',Auth::user()->entidad_id)
      ->get();
      $output="";
      foreach($data as $row)
      {
        $names=$name." => ".$row->name;
       $output .= '<option value="'.$row->id.'" class="nav-link">'.$name." => ".ucwords($row->name).'</option>';
       $output .= ArticulosController::heredada($row->id,$names);

      }
      return $output;
  }
  //
  public function guardarArticulo(Request $r){
    $rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'Error inesperado'; $rta['id'] =null; $rta['listas'] =null;$rta['articulos'] =null; $rta['articulo_lista'] =null;
    $rta['articulo_imagenes'] =null;
    $validator = Validator::make($r->all(), [
      'id_articulo' => 'required',
      'categoria' => 'required',
      // 'costo_articulo' => 'numeric',
      // 'desc_articulo' => 'required',
      // 'desc_co_articulo' => 'required',
      'es_combo' => 'required',
      'existencia' => 'required|numeric',
      'nombre_articulo' => 'required',
      'nombre_co_articulo' => 'required',
      // 'obs_articulo' => 'required',
      // 'obs_co_articulo' => 'required',
      // 'presentacion' => 'required',
      // 'costo_antes' => 'numeric',
      'costo_ahora' => 'required|numeric',
      'tiempo_entrega' => 'required',
      // 'tiempo_entrega_id' => 'required',
      // 'desde' => 'required',
      // 'hasta' => 'required',
      // 'precio' => 'numeric',
      'empresa' => 'required',
      'existencia_minima' => 'required',

  ]);
  if ($validator->fails()) {
    $messages = $validator->errors();
    $rta['des_retorno'] = ''.$messages;
  }else{
    if ($r->id_articulo == 'nuevo') {
      if ($r->cod_articulo==null || empty( $r->cod_articulo)) { $r->cod_articulo=null;}
      if ($r->costo_articulo==null || empty( $r->costo_articulo)) { $r->costo_articulo=null;}
      if ($r->desc_articulo==null || empty( $r->desc_articulo)) { $r->desc_articulo=null;}
      if ($r->desc_co_articulo==null || empty( $r->desc_co_articulo)) { $r->desc_co_articulo=null;}
      if ($r->obs_articulo==null || empty( $r->obs_articulo)) { $r->obs_articulo=null;}
      if ($r->obs_co_articulo==null || empty( $r->obs_co_articulo)) { $r->obs_co_articulo=null;}
      if ($r->costo_antes==null || empty( $r->costo_antes)) { $r->costo_antes=null;}
      if ($r->desde==null || empty( $r->desde)) { $r->desde=null;}
      if ($r->hasta==null || empty( $r->hasta)) { $r->hasta=null;}
      if ($r->precio==null || empty( $r->precio)) { $r->precio=null;}
      $sabores=null;
    $medidas=null;
      //en el caso de que exista un sabor
      if ($r->sabores==null || empty( $r->sabores)) {
        $sabores=null;
      }else{
        $sabor=null;
        foreach ($r->sabores as $key => $value) {
          $sabor.= $r->sabores[$key].",";
        }
        $sabores = substr($sabor,0 , -1);
      }
      //una medida
      if ($r->medidas==null || empty( $r->medidas)) {
        $medidas=null;
      }else{
        $medi=null;
        foreach ($r->medidas as $key => $value) {
          $medi.= $r->medidas[$key].",";
        }
        $medidas = substr($medi, 0 , -1);
      }
      Log::info($sabores);
    $data = Array(
      'name'=>$r->nombre_articulo,
      'descripcion'=>$r->desc_articulo,
      'name_co'	=>$r->nombre_co_articulo,
      'descripcion_co'=>$r->desc_co_articulo,
      'valoracion'=>null,
      'codigo' =>	$r->cod_articulo,
      'presentacion' =>	$r->presentacion,
      'barra'	=>null,
      'empresa_id'	=>$r->empresa,
      'observaciones'	=>$r->obs_articulo,
      'observaciones_co'=>$r->obs_co_articulo ,
      'es_activo'=> $r->es_activo,
      'costo'	=>$r->costo_articulo,
      'precio_venta'	=> $r->costo_ahora,
      'existencia'=>$r->existencia,
      'existencia_minima'=>$r->existencia_minima,
      'ultima_compra'	=>null,
      'ultima_venta'=>null,
      'precio_antes'=>$r->costo_antes,
      'plazo_entrega'=>$r->tiempo_entrega,
      'plazo_entrega_id'=>$r->tiempo_entrega_id,
      'unidad_de_medida'=>$r->unidad_de_medida,
      'timer_precio'=>$r->precio,
      'timer_desde'=>$r->desde,
      'timer_hasta'=>$r->hasta,
      'es_combo'=>$r->es_combo,
      'categoria_id'=>$r->categoria,
      'sabores'=>$sabores,
      'medidas'=>$medidas,
      'colores'=>$r->colores,
      'created_at'=> 'now()',
      'created_by'=> Auth::user()->id,
  );
  try {
    $id = DB::table('articulos')->insertGetId($data);
    Log::info(DB::getQueryLog());
    if ($id > 0 ) {
      $rta['cod_retorno'] = 200;
      $rta['des_retorno'] = 'Insertado con exito ,Por favor inserte a hora las imagenes';
      $rta['id'] = $id;
    }
  } catch (\Throwable $th) {
    Log::info('El query dio error =>'.$th->getMessage());
    $rta['des_retorno'] = 'Error al querer insertar el articulo';
      }
    }else{
      //significa que ya exite y hay que atualizar
      $actulizar= $this->ArticuloActualizar($r);
      if ($actulizar['cod_retorno'] == 200) {
        $rta['cod_retorno'] = 200;
        $rta['des_retorno'] = 'Actualizado con exito';
        $rta['id']=$r->id_articulo;
      }
    }
    $rta['listas'] =$listas = DB::table('listas_precios')->get();
    $rta['articulos'] =$articulos = Articulos::All();
    $rta['articulos_datatable']=
     DB::table('articulos')->
    join('categorias','categorias.id','=','articulos.categoria_id')
    ->select('articulos.id','articulos.name','articulos.name_co','articulos.codigo','articulos.precio_venta','articulos.existencia')
    ->selectRaw('categorias.name as categoria')
    ->get();
    $rta['articulo_lista'] = $articulo_lista = DB::table('articulos_listas_precios')->get();
    $rta['articulo_imagenes'] =$articulo_imagenes = DB::table('articulo_imagenes')->get();
    $rta['etiquetas'] = $etiquetas = DB::table('etiquetas')->get();

  }
    return $rta;

  }
  public function eliminar_imagen(Request $r){
    Log::info(__FUNCTION__); $rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'Error inesperado';

    Log::info('eliminar=>'.public_path(). '\storage\articulos\\'.$r->id);
    if( file_exists((public_path(). '\storage\articulos\\'.$r->id)) ){
      unlink(public_path().'\storage\articulos\\'.$r->id); //borramos del disco
    }else{
      Log::info('No se econtro');
    }
    $D=DB::table('articulo_imagenes')->where('path', '=', $r->id)->delete();
    Log::info(DB::getQueryLog());
    if ($D > 0) {
      $articulo_imagenes = DB::table('articulo_imagenes')->get();
      $rta['cod_retorno'] = 200;  $rta['des_retorno'] = 'Eliminado';$rta['dta']=$articulo_imagenes;
    }else{
      $rta['des_retorno']=('Hubo un problema al querer eliminar la imagen');
    }
    return $rta;

  }
  public function eliminar(Request $r){
    Log::info(__FUNCTION__);
    $rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'Error inesperado';
    $images=DB::table('articulo_imagenes')->select('path')->where('articulo_id', '=', $r->id)->get();
    if (!$images->isEmpty()) {
      foreach ($images as $img ) {
        Log::info('eliminar=>'.public_path(). '\storage\articulos\\'.$img->path);
        if( file_exists((public_path(). '\storage\articulos\\'.$img->path)) ){
          unlink(public_path().'\storage\articulos\\'.$img->path); //borramos del disco
        }else{
          Log::info('No se econtro');
        }
      }
    }
    try {
      $D=DB::table('articulo_imagenes')->where('articulo_id', '=', $r->id)->delete();
    $l=DB::table('articulos_listas_precios')->where('articulo_id', '=', $r->id)->delete();

    DB::table('combo_articulos')->where('articulo_id', '=', $r->id)->delete();
    DB::table('combo_articulos')->where('combo_id', '=', $r->id)->delete();

    $a=DB::table('articulos')->where('id', '=', $r->id)->delete();
    Log::info(DB::getQueryLog());
    if ($a > 0) {
      $rta['cod_retorno'] = 200;  $rta['des_retorno'] = 'Eliminado';
    }else{
      $rta['des_retorno'] = 'Hubo un problema al querer eliminar el registro';
    }
    } catch (\Throwable $th) {
      Log::info('El query dio error =>'.$th->getMessage()."alan");

      if(strpos(strval($th->getMessage()),'viola la llave foránea')){
        $rta['des_retorno']="No se puede eliminar el articulo porque esta siendo utilizado";
      }else{
        $rta['des_retorno'] = 'Hubo un problema al querer eliminar el registro';
      }

    }

    return $rta;

  }
  public function ArticuloActualizar($data){
    Log::info(__FUNCTION__);
    $rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'Error inesperado';
      $etiquetas_id=null;
      if ($data->etiqueta_id != "sin_etiqueta") {
        $etiquetas_id= $data->etiqueta_id;
      }
      $datos= json_decode($data->lista_precio);
      //
      if ($data->cod_articulo==null || empty( $data->cod_articulo)) { $data->cod_articulo==null;}
      if ($data->costo_articulo==null || empty( $data->costo_articulo)) { $data->costo_articulo==null;}
      if ($data->desc_articulo==null || empty( $data->desc_articulo)) { $data->desc_articulo==null;}
      if ($data->desc_co_articulo==null || empty( $data->desc_co_articulo)) { $data->desc_co_articulo==null;}
      if ($data->obs_articulo==null || empty( $data->obs_articulo)) { $data->obs_articulo==null;}
      if ($data->obs_co_articulo==null || empty( $data->obs_co_articulo)) { $data->obs_co_articulo==null;}
      if ($data->costo_antes==null || empty( $data->costo_antes)) { $data->costo_antes==null;}
      if ($data->desde==null || empty( $data->desde)) { $data->desde==null;}
      if ($data->hasta==null || empty( $data->hasta)) { $data->hasta==null;}
      if ($data->precio==null || empty( $data->precio)) { $data->precio==null;}
      //
    $sabores=null;
    $medidas=null;
      if ($data->sabores==null || empty( $data->sabores)) {
        $sabores=null;
      }else{
        $sabor=null;
        foreach ($data->sabores as $key => $value) {
          $sabor.= $data->sabores[$key].",";
        }
        $sabores = substr($sabor,0 , -1);
      }
      if ($data->medidas==null || empty( $data->medidas)) {
        $medidas=null;
      }else{
        $medi=null;
          foreach ($data->medidas as $key => $value) {
          $medi.= $data->medidas[$key].",";
        }
        $medidas = substr($medi, 0 , -1);
      }

    try {
      $updated=DB::table("articulos")
          ->where("id", '=', $data->id_articulo)
          ->update([
            'name'=>$data->nombre_articulo,
            'descripcion'=>$data->desc_articulo,
            'name_co'	=>$data->nombre_co_articulo,
            'descripcion_co'=>$data->desc_co_articulo,
            'valoracion'=>null,
            'presentacion' =>	$data->presentacion,
            'codigo' =>	$data->cod_articulo,
            'barra'	=>null,
            'empresa_id'	=>$data->empresa,
            'observaciones'	=>$data->obs_articulo,
            'observaciones_co'=>$data->obs_co_articulo ,
            'es_activo'=> $data->es_activo,
            'costo'	=>$data->costo_articulo,
            'precio_venta'	=> $data->costo_ahora,
            'existencia'=>$data->existencia,
            'existencia_minima'=>$data->existencia_minima,
            'ultima_compra'	=>null,
            'ultima_venta'=>null,
            'precio_antes'=>$data->costo_antes,
            'plazo_entrega'=>$data->tiempo_entrega,
            'plazo_entrega_id'=>$data->tiempo_entrega_id,
            'unidad_de_medida'=>$data->unidad_de_medida,
            'etiqueta_id'=>$etiquetas_id,
            'timer_precio'=>$data->precio,
            'timer_desde'=>$data->desde,
            'timer_hasta'=>$data->hasta,
            'es_combo'=>$data->es_combo,
            'categoria_id'=>$data->categoria,
            'sabores'=>$sabores,
            'medidas'=>$medidas,
            'colores'=>$data->colores,
            'updated_at'=> 'now()',
            'updated_by'=> Auth::user()->id,
          ]);
      if ($updated > 0) {
        if ( (count($datos)) > 0 ) {
          $lista=$this->articulo_lista($data->id_articulo,$datos);
            if ($lista['cod']==200) {
              $rta['cod_retorno'] = 200;
              $rta['des_retorno'] = 'todo ok';
            }else{
              $rta['des_retorno'] = 'hubo un problema al insertar la lista de precios';
            }
        }else{
          $rta['cod_retorno'] = 200;
          $rta['des_retorno'] = 'todo ok';
        }

        }

    } catch (\Throwable $th) {
      Log::info('El query dio error =>'.$th->getMessage());
      $rta['des_retorno'] = 'Error al querer Actualizar el articulo';
    }


    return $rta;
  }
 public function articulo_lista($id,$listas){
  Log::info(__FUNCTION__);  $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';
  //por ahora eliminamos y volvemos a crear
  $eliminar = DB::table('articulos_listas_precios')->where('articulo_id',$id)->delete();
  foreach ($listas as $lista) {
    unset($data);
    if ($lista->valor==null) {
      $lista->valor=0;
    }
    $data = Array(
        'articulo_id'=> $id,
        'lista_id'=>( $lista->id),
        'costo' =>  ($lista->valor),
        'created_at'=> 'now()',
        'created_by'=> Auth::user()->id
    );
    try {
        DB::table('articulos_listas_precios')->insert($data);
        Log::info(DB::getQueryLog());
        $rta['cod'] = 200;

    } catch (\Throwable $th) {
      Log::info('El query dio error =>'.$th->getMessage());
      $rta['cod'] = 500;
      $rta['msg'] = 'error al insertar la lista de precio';

    }

  }
    return $rta;

 }

public function guardarimagen(Request $r){
    Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';

      $patch=public_path('storage/articulos');
      $max_ancho = 300;
      $max_alto = 300;
      $min_ancho =250;
      $min_alto = 250;
      $medidasimagen= getimagesize($r->file);
      // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
      if ($r->file->extension() == "svg") {
        $filename = time() . "." . $r->file->extension();
        $r->file->move(public_path('storage/articulos'), $filename);
      }
      elseif ($medidasimagen[0] < 300 && $medidasimagen[1] < 300 && (filesize($r->file)) < 100000){
        $filename = time() . "." . $r->file->extension();

        $r->file->move(public_path('storage/articulos'), $filename);

      }else{

      $filename = time() . "." . $r->file->extension();
      // Redimensionar
      // dd($r->file->getPathName());
      $rtOriginal=$r->file->getPathName();

      if($r->file->getClientMimeType() =='image/jpeg'){
      $original = imagecreatefromjpeg($rtOriginal);
      }
      else if($r->file->getClientMimeType() =='image/png'){
      $original = imagecreatefrompng($rtOriginal);
      }
      else if($r->file->getClientMimeType() =='image/gif'){
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
      $ancho_final = 300;
    }
    if (($alto <= $min_alto) || ($alto >= $min_alto)) {
      $alto_final = 300;
    }

      $lienzo=imagecreatetruecolor($ancho_final,$alto_final);

      imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

      //imagedestroy($original);

    $cal=8;

    if($r->file->getClientMimeType()=='image/jpeg'){
    imagejpeg($lienzo,$patch."/".$filename);
    }
    else if($r->file->getClientMimeType()=='image/png'){
    imagepng($lienzo,$patch."/".$filename);
    }
    else if($r->file->getClientMimeType()=='image/gif'){
    imagegif($lienzo,$patch."/".$filename);
    }

    }
    if (isset($filename) && !empty($filename)) {
      try {
        $orden=DB::table('articulo_imagenes')->where('articulo_id',$r->id)->orderBy('orden','desc')->first();
        if (empty($orden)) {
          $orden = 1;
        }else{
          $orden=($orden->orden + 1);
        }
        $data = Array(
          'articulo_id'=>  $r->id,
          'path'=> $filename,
          'orden'=> $orden,
          'created_at'=> 'now()',
          'created_by'=> Auth::user()->id
      );
        $insertar=DB::table('articulo_imagenes')->insert($data);
        if ($insertar > 0) {
          $articulo_imagenes = DB::table('articulo_imagenes')->orderBy('id','asc')->orderBy('orden','asc')->get();
          $rta['cod'] = 200;
          $rta['msg'] = 'todo ok';
          $rta['dta'] =$articulo_imagenes ;
        }
      } catch (\Throwable $th) {
        $rta['msg'] = 'hubo un problema al registrar la imagen';
      }
    }else{
      $rta['msg'] = 'error no se pudo cargar la imagen';
    }


  return $rta;
}




  //Categoria de articulos
  public function indexOrdenar(){

    $empresas = DB::table('empresa')
    ->whereNull('deleted_at')
    ->get();
    $categorias = DB::table('categorias')
    ->whereNull('deleted_at')
    ->get();
    $selectdos=$this->selected();
    // dd($selectdos);
    return view('admin.categorias.index_ordenar',['empresas' => $empresas,'categorias' => $categorias,'selectdos' => $selectdos]);
}
public function colador(Request $r){
  Log::info(__FUNCTION__);
  $r->validate([
      'categoria' => 'required',
  ]);
  // $empresa =$r->empresa;
  $categoria = $r->categoria;
  $sql = "
  select c.id, c.name, c.name_co,(select name from categorias where id= c.padre) as padre,c.padre as padre_id,i.id as icono_id, i.path,c.path as imagen,c.orden
  from categorias c
  left join iconos i on i.id=c.icono_id
  where c.activo = true
  order by c.name , c.orden, c.padre
  ";
  $rs = DB::select($sql);
  $categorias=$rs;
    $categoria_name=null;
    if ($categoria != 0) {
      $categoria_name = DB::table('categorias')
      ->select('name')
      ->where('id',$categoria)
      ->whereNull('deleted_at')
      ->first();
    }
    $selectdos=$this->selected();
  return view('admin.categorias.index_ordenar',['selectdos' => $selectdos,'categoria_name' => $categoria_name,'categorias' => $categorias,'categoria' => $categoria ])->with('status', 'Listando');

}

  public function index(Request $request)
  {
    $listas = DB::table('listas_precios')->get();
    $sql = "
    select c.id, c.name, c.name_co,(select name from categorias where id= c.padre) as padre,c.padre as padre_id,i.id as icono_id, i.path,c.path as imagen,c.orden
    from categorias c
	  left join iconos i on i.id=c.icono_id
    where c.activo = true
    order by c.name , c.orden, c.padre
    ";
    $rs = DB::select($sql);
    $categorias=$rs;
    $categorias_anidadas= $this->selected();
    // $categorias= $this->getComboAnidado("categorias");
    $inconos=Iconos::orderBy("name")->get();
    // dd($categorias);
    return view('/admin.articulos.index_momentano',['categorias' => $categorias,'iconos' => $inconos,'anidada' => $categorias_anidadas,'categorias_lista' => $listas]);

  }
  public function getAnidado(Request $r){
    $table=$r->table;
    $menues = $this->getComboAnidado($table);
    $listas=$this->selected();
    $array_list=array('combo'=>$menues,'lista'=>$listas);
    return response()->json($array_list);
}

public function getComboAnidado($table){
  Log::info(__FUNCTION__);
  try {
    $sql = "
    select categorias.id, categorias.name,categorias.name_co, iconos.id as icon_id,iconos.name as icon ,iconos.path
    from ".$table."
    join iconos on iconos.id =categorias.icono_id
    where categorias.activo = true
    and categorias.padre is null
   order by categorias.padre, categorias.orden
    ";

$rs = DB::select($sql); $menues = $menu = Array();
foreach($rs as $r){
$menu['id'] = $r->id;
$menu['text'] = $r->name."/".$r->name_co;
$menu['state'] = array('opened' => 'true');
$menu['children'] = $this->getSubMenu($r->id,$table);
$menues[] = $menu;
}
  } catch (\Throwable $th) {
    Log::info('El query dio error =>'.$th->getMessage());
  }
    return $menues;

}
private function getSubMenu($id,$table){
    $sql = "
    select categorias.id, categorias.name,categorias.name_co, iconos.id as icon_id,iconos.name as icon ,iconos.path
    from ".$table."
    join iconos on iconos.id =categorias.icono_id
    where categorias.activo = true
    and padre = ".$id."
    order by categorias.padre, categorias.orden
    ";

    $rs = DB::select($sql); $menues =$menu = Array();
    foreach($rs as $r){
        $menu['id'] = $r->id;
        $menu['text'] = $r->name."/".$r->name_co;
        $menu['state'] = array('opened' => 'true');
        $menu['children'] = $this->getSubMenu($r->id,$table);
        $menues[] = $menu;
    }
    return $menues;
  }

  public function guardar_categoria_momentanea(Request $r){
    Log::info(__FUNCTION__); Log::info($r);
    try {
    
    $r->validate([
      'name' => 'required',
      'name_co' => 'required',
      'padre' => 'required',
      'icono_id' => 'required',
  ]);
  // dd($r->id_categoria);
  if (!is_null($r->id_categoria)) {
    $respuesta=$this->update_momentaneo($r);
    if ($respuesta['cod'] >200 ) {
      return back()->withErrors('Hubo un problema en el proceso');
    }else{
      return back()->with('status', 'Exito al actualizar la categoria');
    }
  }else{
    $filename=null;
  if(!empty($r->path)){
    $respueta=$this->guardarimagen_categoria($r->path);
    $filename=$respueta['data'];
  }

    $icono=null;
    if ($r->icono_id != 0) {
      $icono=$r->icono_id;
    }
  if ($r->padre == "0") {
    //es uno nuevo
    $ultimo= DB::table('categorias')->select('orden')->whereNull('padre')->orderBy('orden','desc')->limit(1)->first();
    if (empty($ultimo)) {
      $orden= 1;
    }else{
      $orden=$ultimo->orden + 1;
    }
    $data = Array(
      'name'=>$r->name,
      'name_co'=>$r->name_co,
      'icono_id'=> $icono,
      'path'=>$filename,
      'padre'=>null,
      'orden'=>$orden,
      'activo'=>true,
      'created_at'=> 'now()',
      'created_by'=> Auth::user()->id,
  );
  }else{
    $ultimo= DB::table('categorias')->select('orden')->where('padre', $r->padre)->orderBy('orden','desc')->limit(1)->first();
    if (empty($ultimo)) {
      $orden= 1;
    }else{
      $orden=$ultimo->orden + 1;
    }
    $data = Array(
      'name'=>$r->name,
      'name_co'=>$r->name_co,
      'icono_id'=> $icono,
      'padre'=>$r->padre,
      'path'=>$filename,
      'orden'=>$orden,
      'activo'=>true,
      'created_at'=> 'now()',
      'created_by'=> Auth::user()->id,
  );
  }
  try {
    $id = DB::table('categorias')->insertGetId($data);
    // return redirect('/admin/images/'.($r->tabla_name))->with('status', 'Icono cargado con exito');
    if ($id > 0 ) {
      return back()->with('status', 'Exito al guardar la categoria');
    }

} catch (\Throwable $th) {
    Log::info('El query dio error =>'.$th->getMessage());
    return back()->withErrors('Hubo un problema en el proceso');
    }
  }
} catch (\Throwable $th) {
  Log::error("error".$th->getMessage());
  return back()->withErrors('Hubo un problema en el proceso');
}
}
// alter table categorias add path character varying;
// alter table categorias alter COLUMN path drop not null
public function update_momentaneo($r){
  Log::info(__FUNCTION__); Log::info($r); $rta["cod"]=500;
  $filename=null;
  try {
  
  //
  if(!empty($r->path)){
  $path=DB::table("categorias")->select('path')->where("id", '=', $r->id_categoria)->first();
  if (isset($path->path) && $path->path !=null) {
    Log::info('eliminar=>'.public_path(). '\storage\categorias\\'.$path->path);
    if( file_exists((public_path(). '\storage\categorias\\'.$path->path)) ){
    unlink(public_path().'\storage\categorias\\'.$path->path); //borramos del disco
    }else{
    Log::info('No se econtro');
    }
  }
    $respueta=$this->guardarimagen_categoria($r->path);
    $filename=$respueta['data'];

}

  //
  $icono=$r->icono_id;
  Log::info($icono);
  if ($r->padre == "0") {
    Log::info("supuestamente nuevo o de una categotia principal");
    //es uno nuevo
    $ultimo= DB::table('categorias')->select('orden')->whereNull('padre')->orderBy('orden','desc')->limit(1)->first();
    if (empty($ultimo)) {
      $orden= 1;
    }else{
      $orden=$ultimo->orden + 1;
    }
    $data = Array(
      'name'=>$r->name,
      'name_co'=>$r->name_co,
      'icono_id'=> $icono,
      'padre'=>null,
      'orden'=>$orden,
      'activo'=>true,
      'updated_at'=> 'now()',
      'updated_by'=> Auth::user()->id,
  );
  if ($filename !=null) {
    $mege=array('path'=>$filename);
    $data=array_merge($data,$mege);
  }
  }else{
    $ultimo= DB::table('categorias')->select('orden')->where('padre', $r->padre)->orderBy('orden','desc')->limit(1)->first();
    if (empty($ultimo)) {
      $orden= 1;
    }else{
      $orden=$ultimo->orden + 1;
    }
    $data = Array(
      'name'=>$r->name,
      'name_co'=>$r->name_co,
      'icono_id'=> $icono,
      'padre'=>$r->padre,
      'orden'=>$orden,
      'path'=>$filename,
      'activo'=>true,
      'updated_at'=> 'now()',
      'updated_by'=> Auth::user()->id,
  );
  if ($filename !=null) {
    $mege=array('path'=>$filename);
    $data=array_merge($data,$mege);
  }
  
  }
    
      $id=DB::table("categorias")
      ->where("id", '=', $r->id_categoria)
      ->update($data);
      if ($id > 0 ) {
         $rta["cod"]=200;
      }else{
         $rta["cod"]=500;
      }
     
       
    } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
         $rta["cod"]=500;
        // return back()->withErrors('Hubo un problema en el proceso');
    }
    return $rta;
}




    public function guardar_categoria(Request $r){
      Log::info(__FUNCTION__); Log::info($r);
$rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'hubo un error inesperado'; $rta['select'] =null;

   $validator = Validator::make($r->all(), [
       'name_categoria' => 'required',
       'name_categoria_co' => 'required',
       'icon' => 'required',
       'id_padre' => 'required',

   ]);
   if ($validator->fails()) {
      $rta['des_retorno'] = 'Complete los campos requeridos';
     return $rta;
   }else{
      Log::info("paso sin el explode");
        if ($r->id_padre=="sumar") {//quiere decir que es nuevo
            $rta['tipomenu']=1;
            $rta["data"]=null;
        }else{//es un nuevo submenu para submenu
            Log::info("hijito");
            $rta['tipomenu']=2;
            $rta["data"]=$r->id_padre;
        }

      if ($rta["data"] == null &&  $rta['tipomenu'] == 1 ) { //insertamos nuevo
        Log::info("entro nuevito");
        $data = Array(
            'name'=>$r->name_categoria,
            'name_co'=>$r->name_categoria_co,
            'icono_id'=>$r->icon,
            'padre'=>null,
            'activo'=>true,
            'created_at'=> 'now()',
            'created_by'=> Auth::user()->id,
        );

    }else{
   Log::info("segunda condicion");
          if ($rta["data"] <> null ) { //insertamos un hijo a un padre
              Log::info("entro nuev hijo");
              $data = Array(
                  'name'=>$r->name_categoria,
                  'name_co'=>$r->name_categoria_co,
                  'icono_id'=>$r->icon,
                  'padre'=>intval($rta["data"]),
                  'activo'=>true,
                  'created_at'=> 'now()',
                  'created_by'=> Auth::user()->id,
              );
          }
        }

       try {
         $id = DB::table('categorias')->insertGetId($data);
         Log::info($id);
         if ($id > 0) {
          $rta['cod_retorno'] = 200;
          $rta['des_retorno'] = 'todo ok';
          $rta['select']= $this->getComboAnidado("categorias");
         }

       } catch (\Throwable $th) {
         Log::info('El query dio error =>'.$th->getMessage());
         $rta['des_retorno'] = 'hubo un error inesperado';
       }

       }
       return $rta;

   }
   public function delete(Request $r){
    Log::info(__FUNCTION__);
    try {
       //primero eliminaremos el principal
        $foo['articulo_categoria'] = DB::table('categorias')
              ->where('id', '=', $r->id)
              ->delete();
              Log::info(DB::getQueryLog());
        //luego eliinamos los hijos
    $foo['articulo_categoria_hijos'] = DB::table('categorias')
              // ->where('entidad_id', '=', Auth::user()->entidad_id)
              ->where('padre', '=', $r->id)
              ->delete();
              Log::info(DB::getQueryLog());
              log::ingo("todo ok");
            return redirect('/categoria/articulos/');
    } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
        return back()->with("error","hubo un error inesperado");
    }

  }
  public function guardarimagen_categoria($dataa){
    Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';
    Log::info("paso por aqui");

      $patch=public_path('storage/categorias');
      $max_ancho =300;
      $max_alto = 300;
      $min_ancho =100;
      $min_alto = 100;
      $medidasimagen= getimagesize($dataa);
      $info = new SplFileInfo($dataa);
      // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
      if ($dataa->extension() == "svg") {
        $filename = time() . "." . $dataa->extension();
        $dataa->move(public_path('storage/categorias'), $filename);
      }
      elseif ($info->getExtension() == "tmp"){
        Log::info("paso por aqui2");
        $filename = time() . "." . $dataa->extension();

        $dataa->move(public_path('storage/categorias'), $filename);

      }elseif($medidasimagen[0] < 400 && $medidasimagen[0] > 300 && $medidasimagen[1] > 90 && $medidasimagen[1] < 150 && (filesize($dataa)) < 100000){
        $filename = time() . "." . $dataa->extension();

        $dataa->move(public_path('storage/categorias'), $filename);

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
    Log::info($rta);

  return $rta;
}
  public function ordenar(Request $r){
    Log::info(__FUNCTION__); Log::info($r);$rta['cod'] = 500; $rta['msg'] = 'hubo un error al ordenar las imagenes';
    $datos = json_decode($r->data);
    Log::info($datos);
    foreach ($datos as $key => $value) {
    unset($data);
    $data = Array(
      'orden'=>$datos[$key]->orden,
      'updated_at'=> 'now()',
      'updated_by'=> Auth::user()->id,
    );
  try {
    $id=DB::table("articulo_imagenes")
    ->where("id", '=', $datos[$key]->id_tabla)
    ->update($data);
    Log::info(DB::getQueryLog());
    if ($id > 0 ) {
      $rta["cod"]=200;
      $rta['msg']="Orden Corregido";
      // $sql = "
      // select c.id, c.name, c.name_co,(select name from categorias where id= c.padre) as padre,c.padre as padre_id,i.id as icono_id, i.path,c.path as imagen,c.orden
      // from categorias c
      // left join iconos i on i.id=c.icono_id
      // where c.activo = true
      // order by c.name , c.orden, c.padre
      // ";
      // $rta['dta'] = DB::select($sql);
      $rta['articulo_imagenes']= DB::table('articulo_imagenes')->orderBy('id','asc')->orderBy('orden','asc')->get();

    }else{
      $rta["cod"]=500;
    }
    } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
        return $rta["cod"]=500;
        $rta['msg'] = 'hubo un error al ordenar las imagenes';
    }
    }
    return $rta;
  }
  public function ordenarCategorias(Request $r){
    Log::info(__FUNCTION__); Log::info($r);$rta['cod'] = 500; $rta['msg'] = 'hubo un error al ordenar las categorias';
    $datos = json_decode($r->data);
    Log::info($datos);
    foreach ($datos as $key => $value) {
    unset($data);
    $data = Array(
      'orden'=>$datos[$key]->orden,
      'updated_at'=> 'now()',
      'updated_by'=> Auth::user()->id,
    );
  try {
    $id=DB::table("categorias")
    ->where("id", '=', $datos[$key]->id_tabla)
    ->update($data);
    Log::info(DB::getQueryLog());
    if ($id > 0 ) {
      $rta["cod"]=200;
      $rta['msg']="Orden Corregido";
      $sql = "
      select c.id, c.name, c.name_co,(select name from categorias where id= c.padre) as padre,c.padre as padre_id,i.id as icono_id, i.path,c.path as imagen,c.orden
      from categorias c
      left join iconos i on i.id=c.icono_id
      where c.activo = true
      order by c.name , c.orden, c.padre
      ";
      $rta['dta'] = DB::select($sql);
      // $rta['articulo_imagenes']= DB::table('articulo_imagenes')->orderBy('id','asc')->orderBy('orden','asc')->get();

    }else{
      $rta["cod"]=500;
    }
    } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
        return $rta["cod"]=500;
        $rta['msg'] = 'hubo un error al ordenar las imagenes';
    }
    }
    return $rta;
  }
  public function ordenarcolores(Request $r){
    Log::info(__FUNCTION__); Log::info($r);$rta['cod'] = 500; $rta['msg'] = 'hubo un error con los colores ';
    $datos = json_decode($r->data);
    if (empty($datos->colores)) {
      Log::info("------------vacio");
      try {
        $id = DB::table("articulos")
        ->where("id", '=', $datos->id)
        ->update([
          'colores' => null,
          'updated_at'=> 'now()',
          'updated_by'=> Auth::user()->id,
        ]);
        $rta['msg'] = 'colores actualizados';
        $rta["cod"]=200;
        $rta['articulos'] = $articulos =Articulos::All();
        Log::info(DB::getQueryLog());
      } catch (\Throwable $th) {
        Log::info('El query dio error =>'.$th->getMessage());
        $rta['msg'] = 'hubo un error con los colores';
        $rta["cod"]=500;
        return $rta;
      }
    }else{
      Log::info("------------lleno");
        $colores = $datos->colores;
        $color=null;
        $colors="";
        foreach ($colores as $key => $value) {
          $color.= $colores[$key]->name.",";
        }
        $colors = substr($color,0 , -1);
        $data = Array(
          'colores'=>$colors,
          'updated_at'=> 'now()',
          'updated_by'=> Auth::user()->id,
        );
      try {
        $id=DB::table("articulos")
        ->where("id", '=',  $datos->id)
        ->update($data);
        Log::info(DB::getQueryLog());
        if ($id > 0 ) {
          $rta["cod"]=200;
          $rta['msg']="Actualizado";
          $rta['articulos'] = $articulos =Articulos::All();
        }else{
          $rta["cod"]=500;
        }
        } catch (\Throwable $th) {
            Log::info('El query dio error =>'.$th->getMessage());
            return $rta["cod"]=500;
            $rta['msg'] = 'hubo un error con los colores';
        }
    }
    return $rta;
  }

  //actualizacion 2024 abril
  public function articuloDescripcion(Request $r){
  Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
   try { 
  
    $rta['dta']=DB::table('articulo_imagen_descripcion')->where('articulo_id',$r->id)->orderBy('orden','asc')->get();
    $rta['cod']=200; $rta['msg']='ok'; $rta['id']=$r->id;

   } catch (\Throwable $th) {
    Log::error('Error'.$th->getMessage());
   }
   return $rta;
  }
  public function DeleteDescripcion(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
     try { 
      $imagen=DB::table('articulo_imagen_descripcion')->where('id',$r->id)->first();
      Log::info('eliminar=>'.public_path(). '\storage\articulos\\'.$imagen->path);
      if( file_exists((public_path(). '\storage\articulos\\'.$imagen->path)) ){
        unlink(public_path().'\storage\articulos\\'.$imagen->path); //borramos del disco
      }else{
        Log::info('No se econtro');
      }
      DB::table('articulo_imagen_descripcion')->where('id',$r->id)->delete();
      $consulta=DB::table('articulo_imagen_descripcion')->where('articulo_id',$r->id)->orderBy('orden','asc')->get();
      $orden=1;
      foreach ($consulta as $value) {
        DB::table('articulo_imagen_descripcion')
        ->where('articulo_id',$r->id)
        ->where('id',$value->id)
        ->update([
          'orden' => $orden
        ]);
        $orden++;
      }
      $rta['cod']=200; $rta['msg']='Descripcion Eliminada'; 
  
     } catch (\Throwable $th) {
      Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
    public function guardarImagenDescripcion(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
     try { 
      $orden=1;
      foreach ($r->art_img as $value) {
        if($value['manipulado'] == 'true'){
          $img= $this->ImagenDescripcion($value)['filename'];  
        }else{
          $img= $value['path_name'];
        }
        $data = Array(
            'articulo_id'=>  $r->art_des_id,
            'path'=> $img ?? 'no-imagen.jpg',
            'descripcion'=>$value['descripcion'],
            'descripcion_co'=>$value['descripcion_co'],
            'orden'=> $orden,
            'created_at'=> 'now()',
            'created_by'=> Auth::user()->id
        );
        if($value['identificador'] == 'no-id'){
          Log::info("No id.........");
          $insertar=DB::table('articulo_imagen_descripcion')->insert($data);
        }else{
          Log::info("si manipulado.........");
          $actualizar=DB::table('articulo_imagen_descripcion')->where('id',$value['identificador'])->update($data);
        }
        $orden++;
      }
      $rta['cod']=200; $rta['msg']='Actualizado Correctamente'; 
     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
    private function ImagenDescripcion($r){
      Log::info(__FUNCTION__); $rta['cod'] = 500; $rta['msg'] = 'hubo un error inesperado';
      
      if($r['identificador'] != 'no-id'){
        $imagen=DB::table('articulo_imagen_descripcion')->where('id',$r['identificador'])->first();
        Log::info('eliminar=>'.public_path(). '\storage\articulos\\'.$imagen->path);
        if( file_exists((public_path(). '\storage\articulos\\'.$imagen->path)) ){
          unlink(public_path().'\storage\articulos\\'.$imagen->path); //borramos del disco
        }else{
          Log::info('No se econtro');
        }
      }
     
  
  
        $patch=public_path('storage/articulos');
        $max_ancho = 300;
        $max_alto = 300;
        $min_ancho =250;
        $min_alto = 250;
        $medidasimagen= getimagesize($r['file_img']);
        // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
        if ($r['file_img']->extension() == "svg") {
          $filename = time() . "." . $r['file_img']->extension();
          $r['file_img']->move(public_path('storage/articulos'), $filename);
        }
        else{
          $filename = time() . "." . $r['file_img']->extension();
          $r['file_img']->move(public_path('storage/articulos'), $filename);
        }
  
        // elseif ($medidasimagen[0] < 300 && $medidasimagen[1] < 300 && (filesize($r['file_img'])) < 100000){
        //   $filename = time() . "." . $r['file_img']->extension();
  
        //   $r['file_img']->move(public_path('storage/articulos'), $filename);
  
      //   }else{
  
      //   $filename = time() . "." . $r['file_img']->extension();
      //   // Redimensionar
      //   // dd($r['file_img']->getPathName());
      //   $rtOriginal=$r['file_img']->getPathName();
  
      //   if($r['file_img']->getClientMimeType() =='image/jpeg'){
      //   $original = imagecreatefromjpeg($rtOriginal);
      //   }
      //   else if($r['file_img']->getClientMimeType() =='image/png'){
      //   $original = imagecreatefrompng($rtOriginal);
      //   }
      //   else if($r['file_img']->getClientMimeType() =='image/gif'){
      //   $original = imagecreatefromgif($rtOriginal);
      //   }
  
      //   list($ancho,$alto)=getimagesize($rtOriginal);
      //   $x_ratio = $max_ancho / $ancho;
      //   $y_ratio = $max_alto / $alto;
  
  
      // if( ($ancho <= $max_ancho) && ($ancho >= $min_ancho) && ($alto <= $max_alto)  && ($alto >= $min_alto)){
      //     $ancho_final = $ancho;
      //     $alto_final = $alto;
      // }
      // if (($ancho <= $max_ancho) || ($ancho >= $max_ancho)) {
      //   $ancho_final = 300;
      // }
      // if (($alto <= $min_alto) || ($alto >= $min_alto)) {
      //   $alto_final = 300;
      // }
  
      //   $lienzo=imagecreatetruecolor($ancho_final,$alto_final);
  
      //   imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
  
      //   //imagedestroy($original);
  
      // $cal=8;
  
      // if($r['file_img']->getClientMimeType()=='image/jpeg'){
      // imagejpeg($lienzo,$patch."/".$filename);
      // }
      // else if($r['file_img']->getClientMimeType()=='image/png'){
      // imagepng($lienzo,$patch."/".$filename);
      // }
      // else if($r['file_img']->getClientMimeType()=='image/gif'){
      // imagegif($lienzo,$patch."/".$filename);
      // }
  
      // }
      if (isset($filename) && !empty($filename)) {
        $rta['cod'] = 200; $rta['msg'] = 'ok'; $rta['filename']= $filename;      
      }else{
        $rta['msg'] = 'error no se pudo cargar la imagen';
      }
  
  
    return $rta;
  }
}
