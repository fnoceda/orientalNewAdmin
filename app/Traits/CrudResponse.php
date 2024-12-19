<?php

namespace App\Traits;

use App\Http\Controllers\UtilidadesController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

trait CrudResponse
{

    public $table;
    public $attributes;
    public $ruteName;
    public $title;
    public $pks;
    public $fks;

    public function setTable(Request $r){

        $url = explode('/', $r->getPathInfo());
        $uri = '/'.$url[1].'/'.$url[2].'/';
        $datos = DB::table('menus')->where('url', $uri)->first();

        $this->table = $datos->table;
        $this->title = $datos->name;
        $this->ruteName = 'abms';
        $this->rute = $uri;
        $this->attributes = self::describeTable();
        $this->pks = self::getPks();
        $this->fks = self::getFks();
    }
    // Funcion que genera el dataTable desde un ResultSet
    protected function getDataTable($rs){

        return Datatables::of($rs)
            ->addColumn('acciones', function ($r) {
                $desc = (empty($r->descripcion))? '' : $r->descripcion ;
                return '
                <form class="form-check-inline" id="form_'.$r->id.'" action="'.url($this->rute.$r->id).'" method="post">
                '.csrf_field().'
                <input name="_method" type="hidden" value="DELETE">
                <a title="Editar" class="" href= "'.url($this->rute.$r->id).'"><i class="fas fa-edit" title="Editar"></i></a>
                <a title="Ver" onclick=" verRegistro('.$r->id.') " class="" href= "#" style="padding: 0 8px 0 5px;"><i class="fas fa-search" title="Ver"></i></a>
                <a href="'.url($this->rute.$r->id).'" title="Eliminar" type="submit" class=" " data-toggle="modal" data-target="#crudModal" data-form="form_'.$r->id.'" data-titulo="Seguro que desea eliminar?" data-mensaje=" <b>'.$r->id.'-'
                .$desc.
                '</b>"><i class="fas fa-trash"></i></a>
                </form>
                ';
            })
            ->removeColumn('created_at')
            ->removeColumn('updated_at')
            ->removeColumn('deleted_at')
            ->removeColumn('created_by')
            ->removeColumn('updated_by')
            ->removeColumn('deleted_by')

            ->editColumn('id', '{!!$id!!}')
            ->setRowId('id')
            ->setRowData(['id' => 'test',])
            ->setRowAttr(['color' => 'red',])
            ->rawColumns(['attachment', 'acciones'])
            ->make();

    }


    protected function tableColsLables(){
        $columns = self::tableCols(); $lables = array();
        foreach($columns as $column){
            $lables[] = $column['label'];
        }
        return $lables;
    }

    public function getFkOptions($campo, $id=''){
        foreach( $this->fks as $fk ){
            if($fk->campo == $campo){
                $table = $fk->tabla;
            }
        }
        $sql = ' select id, name from '.$table." where 1 = 1 ";
        if(trim($id) != ''){ $sql .= ' and id = '.$id; }
        /* if(!empty(auth()->user()->entidad_id)){ //si no tiene entidad es owner
            if(substr($table, 0, 3) != 'ge_'){
                $sql .= " and entidad_id = ".auth()->user()->entidad_id;
            }
        } */
        $sql .= 'order by 2';
        DB::enableQueryLog();
        //Log::info('getFkOptions');
        $rs = DB::select($sql);
        //Log::info(DB::getQueryLog());
        return $rs;
    }

    protected function tableCols(){
        $array = Array();
        $columns = $this->attributes;  $c=0;
        $pks = (array) $this->pks;
        foreach($columns as $column){
            if(
                ($column->name != 'created_at') && ($column->name != 'updated_at') && ($column->name != 'deleted_at')
                    &&
                ($column->name != 'created_by') && ($column->name != 'updated_by') && ($column->name != 'deleted_by')
                    &&
                //excepcion para el campo name de cl_clientes que se conforma de nombres + apellidos por trigger
                (!(($column->name == 'name') && ($this->table == 'clientes')))
            ){

                $array[$c]['attnum'] = $column->attnum;
                $array[$c]['label'] = empty($column->comentario) ? strtolower($column->name) : strtolower($column->comentario);
                $array[$c]['name'] = strtolower($column->name);
                $array[$c]['type'] = $column->type;
                $array[$c]['required'] = ($column->notnull == 't')? 'required' : '' ;
                $array[$c]['pk'] = ( in_array( $column->name, array_column($pks, 'name') ))? true : false ;
            //    $array[$c]['uk'] = ($column->uniquekey == 't')? true : false ;
             foreach($this->fks as $fk){
                 if($fk->campo == $column->name){
                    $array[$c]['fktable'] = strtolower($fk->tabla);
                    $array[$c]['fkfield'] = strtolower($fk->referencia);
                 }
             }

                $opciones = NULL;
                if(in_array($column->name, array_column($this->fks, 'campo'))){
                    $ops = self::getFkOptions( $column->name );
                    $opciones[] = Array('id'=>NULL, 'descripcion'=>'Elija una opcion');
                    foreach($ops as $op){
                        $opciones[] = Array('id'=>$op->id, 'descripcion'=>$op->name);
                    }
                }else{
                    ////Log::info($column->name.' NO ES FK');
                }

                $array[$c]['opciones'] = $opciones;
            }
            $c++;
        }
        return $array;
    }

   /**
     * Display a data json of the resource.
     *
     * @return \Illuminate\Http\Response
     */
protected function getDependietes(){

//esta parte estaba definid para una sola tabla pero la cambiamos par que elimine todas las dependientes
    $sql = "
        SELECT
        c.relname as tabla,
            f.attname AS campo,
            CASE WHEN p.contype = 'f' THEN g.relname END AS padre,
            CASE
                WHEN p.contype = 'f' THEN (select aa.attname from pg_attribute aa join  pg_class cc on cc.oid = aa.attrelid where cc.relname = c.relname and aa.attnum in (p.confkey[1]::smallint))
            END AS referencia
            FROM pg_attribute f
            JOIN pg_class c ON c.oid = f.attrelid
            JOIN pg_type t ON t.oid = f.atttypid
            LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
            LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY (p.conkey)
            LEFT JOIN pg_class AS g ON p.confrelid = g.oid
            WHERE c.relkind = 'r'::char
            and g.relname = 'cl_cortes'
            and p.contype = 'f'
            AND f.attnum > 0
            ORDER BY f.attnum
    ";

    DB::enableQueryLog();
    //Log::info('getFks');
    $rs = DB::select($sql);
    //Log::info(DB::getQueryLog());
    return $rs;
}
protected function getFks(){
//  get Fks ahun no esta definido

    $sql = "
	SELECT
        f.attname AS campo,
        CASE WHEN p.contype = 'f' THEN g.relname END AS tabla,
        CASE
            WHEN p.contype = 'f' THEN (select aa.attname from pg_attribute aa join  pg_class cc on cc.oid = aa.attrelid where cc.relname = '".$this->table."' and aa.attnum in (p.confkey[1]::smallint))
        END AS referencia
        FROM pg_attribute f
        JOIN pg_class c ON c.oid = f.attrelid
        JOIN pg_type t ON t.oid = f.atttypid
        LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
        LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY (p.conkey)
        LEFT JOIN pg_class AS g ON p.confrelid = g.oid
        WHERE c.relkind = 'r'::char
        AND c.relname = '".$this->table."'
        and p.contype = 'f'
        AND f.attnum > 0 ORDER BY f.attnum
    ";



    DB::enableQueryLog();
    //Log::info('getFks');
    $rs = DB::select($sql);
    //Log::info(DB::getQueryLog());
    return $rs;

}
public function getPks(){
    // getpks no esta definido

    //$entidad = (auth()->user()->perfil == 1)? '' : " and f.attname <> 'entidad_id' " ;

    $sql = "
    SELECT
        pg_attribute.attname as name,
        format_type(pg_attribute.atttypid, pg_attribute.atttypmod) as type
    FROM
        pg_index, pg_class, pg_attribute, pg_namespace
    WHERE
            pg_class.oid = '".$this->table."'::regclass
            AND indrelid = pg_class.oid
            AND nspname = 'public'
            AND pg_class.relnamespace = pg_namespace.oid
            AND pg_attribute.attrelid = pg_class.oid
            AND pg_attribute.attnum = any(pg_index.indkey)
            AND indisprimary
    ";

    DB::enableQueryLog();
    $rs = DB::select($sql);
    //Log::info('getPks'); //Log::info(DB::getQueryLog());
    return $rs;
}


protected function describeTable(){
    //describe table ahun no esta definido

    //Si no es owwenr no es necesario mostrar la entidad
    //$entidad = (auth()->user()->perfil == 1)? '' : " and f.attname <> 'entidad_id' " ;
    $entidad = '';
    $sql = "
        select
            f.attnum,
            f.attname AS name,
            f.attnotnull AS notnull,
            pg_catalog.format_type(f.atttypid,f.atttypmod) AS type,
            pg_catalog.col_description(c.oid,f.attnum) as comentario
        FROM pg_attribute f
            JOIN pg_class c ON c.oid = f.attrelid
            JOIN pg_type t ON t.oid = f.atttypid
        WHERE  c.relname = '".$this->table."'
            ".$entidad."
            AND c.relkind = 'r'::char
            AND f.attnum > 0
            ORDER BY f.attnum
";

    //DB::enableQueryLog();
    $rs = DB::select($sql);
    // dd($rs);
    ////Log::info('describeTable'); //Log::info(DB::getQueryLog());
    return $rs;
}

    protected function getAbmQuery($id = '', $edit = 0){

        Log::info(__FILE__.'/'.__FUNCTION__.'('.$this->table.')');
        $camps = $this->attributes; //describimos la tabla
        $pks = $this->pks; $fks = $this->fks;
        $campos = ''; $where = ''; $tablas = $this->table.' ';
        $i=0; $bSuc = 0;
        foreach($camps as $camp){

            if($camp->name == 'sucursal'){ $bSuc = 1; }

            $desc = (empty($camp->comentario) or ($edit == 1)) ? $camp->name : '"'.$camp->comentario.'"'  ;

            if(in_array($camp->name, array_column($fks, 'campo')) && empty($id) ){//si es FK
                $i++;
                $campos .= (trim($campos) == '') ? '' : ', ';
                foreach($fks as $fk){
                    if($fk->campo == $camp->name){
                        $campos .= 't'.$i.'.name as '.$desc;
                        $tablas .= ' LEFT JOIN '.$fk->tabla.' as t'.$i.' ON t'.$i.'.'.$fk->referencia.' = '.$this->table.'.'.$camp->name;
                    }
                }
            }else{
                if($camp->name == 'name'){

                    $campos .= (trim($campos) == '') ? $this->table.'.'.$camp->name.' as '.$desc : ', '.$this->table.'.'.$camp->name.' as '.$desc;

                }elseif($camp->type == 'boolean'){
                    $campos .= ", (case when ".$this->table.".".$camp->name." = true then 'SI' else 'NO' end) as ".$desc;
                }elseif($camp->type == 'date'){
                    $campos .= ", (case when ".$this->table.".".$camp->name." = null then null else to_char(".$this->table.".".$camp->name.", 'dd/mm/yyyy') end) as ".$desc;
                }else{
                    $campos .= (trim($campos) == '') ? $this->table.'.'.$camp->name : ', '.$this->table.'.'.$camp->name;
                }
            }

        }

        $where .= ' and '.$this->table.'.deleted_at is null ';
        $sql = ' select '.$campos.' from '.$tablas.' where 1 = 1 '.$where;

        //$sql .= (!empty(auth()->user()->entidad_id))? 'and '.$this->table.'.entidad_id = '.auth()->user()->entidad_id : '';

        //$sql .= (!empty(auth()->user()->sucursal) and ($bSuc == 1) )? 'and '.$this->table.'.sucursal = '.auth()->user()->sucursal : '';

        //if( $this->table == 'cl_clientes' ){ $sql .= " and ".$this->table.'.id in (select cliente from cl_contratos where sucursal = '.auth()->user()->sucursal.')'; }




        $sql .= (empty($id)) ? '' : ' and '.$this->table.'.id = '.$id;

        Log::info('resultSQL => '.$sql); //el log del query resultante
        // dd($sql);
        return $sql;
    }

    protected function getValidacion(){
        $campos =  $this->attributes;
        foreach($campos as $campo){
            if
            ($campo->notnull && ( !in_array($campo->name, array('id', 'created_by', 'created_at')) )){
                $camp[$campo->name] = 'required';
            }
        }
        return $camp;
    }

    protected function getRegistro($id, $edit = 1){
       $sql = self::getAbmQuery($id, $edit);
       DB::enableQueryLog();
       $registro = DB::select($sql);
       return $registro;
    }

    protected function getDataJson(){
        $rs = DB::select(self::getAbmQuery());
        return self::getDataTable($rs);
    }

    private function getColsTypes(){
        $campos = $this->attributes;
        foreach($campos as $campo){
            $types[$campo->name] = $campo->type;
        }
        return $types;
    }
    public function actualizar($request){
        DB::enableQueryLog();
        $columnas = self::getColsTypes();
        foreach($request as $key=>$val){
            if(array_key_exists($key, $columnas) ){
                if(($key == 'id')) {
                    $id = $val;
                }else{

                    if(   (trim($val) != '' )  && ($key != '_token')){
                        if(in_array($columnas[$key], array('date'))){
                            $campos[$key] = self::dbDate($val);
                        }else{
                            $campos[$key] = $val;
                        }
                    }
                }
            }
        }
        $campos['updated_at'] = 'now()';
        $campos['updated_by'] = auth()->user()->id;

        $updates = DB::table($this->table)
        ->where('id', '=', $id)
        ->update($campos);

        return $updates;
    }

    public function insertar($request){
        $keys = ''; $vals = ''; $campos = self::getColsTypes();
        // dd($campos,$this->attributes);
        foreach($request as $key=>$val){

            if(   (trim($val) != '' )  && ($key != '_token')){
                $keys .= (trim($keys) == '') ? $key : ', '.$key;
                if(in_array(strtok($campos[$key], ' '), array('character'))){
                    $vals .= (trim($vals) == '') ? "'".$val."'" : ", '".$val."'";
                }elseif(in_array($campos[$key], array('date'))){
                    $vals .= (trim($vals) == '') ? "'".self::dbDate($val)."'" : ", '".self::dbDate($val)."'";
                }else{
                    $vals .= (trim($vals) == '') ? $val : ', '.$val;
                }
            }

        }
        $keys .= ', created_at, created_by';
        $vals  .= ', now(), '.auth()->user()->id;

        // $tablageneral=explode('_',$this->table);
            // if($tablageneral[0]<>"ge"){//esto es para saber si la tabla es general o de cliente
                /* if(auth()->user()->perfil != 1){ //si no es OWNER asignamos la entidad a la que esta logeado
                    $keys .= ', entidad_id';
                    $vals .= ', '.auth()->user()->entidad_id;
                } */
            // }
        $sql = 'insert into '.$this->table.'('.$keys.') values ('.$vals.')';
        DB::enableQueryLog();
        // dd($sql);
        $insert = DB::insert($sql);
        Log::info($sql);


        return $insert;
    }

    public function eliminar($id){
        DB::enableQueryLog();
        $updates = '';
        $campos['deleted_at'] = 'now()';
        $campos['deleted_by'] = auth()->user()->id;

        try {
            if ($this->table == 'empresa') {
                $updates=$this->EliminarTotal($id);
            }else{
                DB::enableQueryLog();
                if(auth()->user()->perfil == 1){
                    //si es OWNER le puede actualizar a quien quiera
                    $updates = DB::table($this->table)
                        ->where('id', '=', $id)
                    ->update($campos);
                }else{
                    $updates = DB::table($this->table)
                        ->where('id', '=', $id)
                        //->where('entidad_id', '=', auth()->user()->entidad_id)
                    ->update($campos);
    
                    //borramos suavemente los registros dependientes es solo para un caso especial
                    $deps = self::getDependietes();
                    Log::info($deps);
                    foreach($deps as $dep){
                        $updates = DB::table($dep->tabla)
                        ->where($dep->campo, '=', $id)
                        //->where('entidad_id', '=', auth()->user()->entidad_id)
                    ->update($campos);
                    }
    
    
                }
            }
            Log::info(DB::getQueryLog());
        }catch(\Throwable $e){
            Log::info('El query dio error =>'.$e->getMessage());
            dd($e);
        }
        //dd('registros=>'.$updates.' id=>'.$id);
        return $updates;

    }
    private function EliminarTotal($id){
        try {
        $u=DB::table('empresa')->where('id', '=', $id)->delete();
        $rta['cod'] = 1; 
        } catch (\Throwable $th) {
            if(utilidadesController::strContains($th->getMessage(), 'foreign')){
                $rta['cod'] = 0; 
                $rta['des_retorno'] = 'hubo un error inesperado';
            }
        } 
        return $rta;
    }
    private function dbDate($cadena){

        $v = explode('/', $cadena);
        return $v[2].'-'.$v[1].'-'.$v[0];

    }

}
