<?php

namespace App\Http\Controllers;

use App\Models\TagsArticulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\DataTables;
use Illuminate\Support\Facades\Validator;


class TagsController extends Controller
{
     
    public function index()
    {
        return view('admin.tags.index');
    }

    public function store(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        Log::info($r);
        $rta['cod'] = 500;
        $rta['msg'] = 'Error de proceso';
        $rta['dat'] = null;
        try {
            request()->validate([
                'name' => 'required',
            ]);

            $data = DB::table('tags')->insert([
                'name' => $r->name,
                'created_by'=>Auth::user()->id,
                'created_at'=>now(),
            ]);
            $rta['cod'] = 200;
            $rta['msg'] = 'Registro exitoso!';
            $rta['dat'] = $data;
        } catch (\Exception $e) {
            Log::error($e->getMessage());   // insert query
        }

        return $rta;
    }

    public function edit(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        try {
            $comi = DB::table('tags')->where('id', $r->id)->first();
            return json_encode($comi);
        } catch (\Throwable $th) {
            Log::error("errror" . $th->getMessage());
        }
    }



    public function update(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        Log::info($r);
        $rta['cod'] = 500;
        $rta['msg'] = 'Error de proceso';
        $rta['dat'] = null;

        try {
            request()->validate([
                'name' => 'required',
            ]);

            $data = DB::table('tags')
                ->where('id', $r->id)
                ->update([
                    'name' => $r->name,
                    'updated_by'=>Auth::user()->id,
                    'updated_at'=>now(),

                ]);
            $rta['cod'] = 200;
            $rta['msg'] = 'Registro Actualizado Exitosamente';
            $rta['dat'] = $data;
        } catch (\Exception $e) {
            Log::error($e->getMessage());   // insert query
        }


        return $rta;
    }

    public function destroy(Request $r)
    {
        Log::info(__FILE__ . '/' . __FUNCTION__);
        Log::info($r);
        $rta['cod'] = 500;
        $rta['msg'] = 'Error de proceso';
        $rta['dat'] = null;

        try {
            DB::table('tags')->where('id', $r->id)->delete();
            $rta['cod'] = 200;
            $rta['msg'] = 'Registro Eliminado Exitosamente';
        } catch (\Exception $e) {
            Log::error($e->getMessage());   // insert query
        }
        return $rta;
    }


    public function datatable()
    {

        Log::info(__FILE__ . '/' . __FUNCTION__);
        $rs =  DB::table('tags')->whereNull('deleted_at')->get();
        return DataTables::of($rs)
            ->addColumn('acciones', function ($rs) {
                $botones = '';
                $botones .= '<button onclick="editar(\'' . route('tags.edit', ['id' => $rs->id]) . '\')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-edit"></i></button>';
                $botones .= '&nbsp;';
                $botones .= '<button onclick="eliminar(' . $rs->id . ', \' ' . csrf_token() . ' \', \'' . route('tags.edit', ['id' => $rs->id]) . '\',  \'' . route('tags.delete') . '\'   )" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                return $botones;
            })
            ->editColumn('id', '{!!$id!!}')
            ->rawColumns(['attachment', 'acciones'])
            ->make(true);
    }

    public function datatableSincronizado(Request $r){
        Log::info(__FILE__ . '/' . __FUNCTION__);

        $rs =DB::table('tags_articulos as ta')
        ->selectRaw('ta.id')
        ->selectRaw('a.name as art_name')
        ->selectRaw('t.name as tag_name')
        ->join('articulos as a','a.id','=','ta.articulo_id')
        ->join('tags as t','t.id','=','ta.tag_id')
        ->whereNull('ta.deleted_at')
        ->get();

        return DataTables::of($rs)
            ->addColumn('acciones', function ($rs) {
                $botones = '';
                $botones .= '<button class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="quitar('.$rs->id.')"><i class="fas fa-trash-alt"></i></button>';
                return $botones;
            })
            ->editColumn('id', '{!!$id!!}')
            ->rawColumns(['attachment', 'acciones'])
            ->make(true);
    }
    public function dataInfo(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); 
     try {
        $response = []; 
        if ($r->type == 'tag') {
           $response=DB::table('tags')
            ->select('id','name as text')
            ->whereNull('deleted_at')
            ->where('name', 'ILIKE', '%'.$r->term.'%')
            ->get();
        }else{
            $response=DB::table('articulos')
            ->whereNull('deleted_at')
            ->select('id')
            ->selectRaw("name||'-'||name_co as text")
            ->where('name', 'ILIKE', '%'.$r->term.'%')
            ->orWhere('name_co', 'ILIKE', '%'.$r->term.'%')
            ->get();
        }
        return response()->json($response);
     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
    
    }
    public function storeUpdate(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
     try { 
        $validator = Validator::make($r->all(),[
            'articulo_id'=> 'required',
            'tag_id'=> 'required',
        ]);
        if ($validator->fails()) {
            $rta['cod'] = 422;
            $rta['msg'] = 'Por favor complete todos los campos';
            $rta['dat'] = $validator->errors();
        }else{
            TagsArticulos::updateOrCreate(
                ['tag_id' => $r->tag_id, 'articulo_id' => $r->articulo_id],
                [
                    'tag_id' => $r->tag_id, 
                    'articulo_id' => $r->articulo_id,
                ]
            );
            $rta['cod']=200; $rta['msg']='Buen trabajo!'; 
        }
     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
    public function remove(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
     try { 
        $response=DB::table('tags_articulos')->where('id',$r->id)->delete();
        $rta['cod']=200; $rta['msg']='Eliminado. Buen trabajo!'; 
     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return $rta;
    }
}
