<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\DataTables;

class DeliveryController extends Controller
{

    public function index()
    {
        return view('admin.delivery.index');
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
                'kilometros' => 'required',
                'importe' => 'required',
            ]);

            $data = DB::table('coste_delivery')->insert([
                'kilometros' => $r->kilometros,
                'importe' => $r->importe,
                'created_by'=>auth()->user()->id,
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
            $comi = DB::table('coste_delivery')->where('id', $r->id)->first();
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
                'kilometros' => 'required',
                'importe' => 'required',
            ]);

            $data = DB::table('coste_delivery')
                ->where('id', $r->id)
                ->update([
                    'kilometros' => $r->kilometros,
                    'importe' => $r->importe,
                    'updated_by'=>auth()->user()->id,
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
            DB::table('coste_delivery')->where('id', $r->id)->delete();
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
        $rs =  DB::table('coste_delivery')->whereNull('deleted_at')->orderBy('kilometros','asc')->get();
        return DataTables::of($rs)
            ->addColumn('acciones', function ($rs) {
                $botones = '';
                $botones .= '<button onclick="editar(\'' . route('delivery.edit', ['id' => $rs->id]) . '\')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-edit"></i></button>';
                $botones .= '&nbsp;';
                $botones .= '<button onclick="eliminar(' . $rs->id . ', \' ' . csrf_token() . ' \', \'' . route('delivery.edit', ['id' => $rs->id]) . '\',  \'' . route('delivery.delete') . '\'   )" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                return $botones;
            })
            ->editColumn('id', '{!!$id!!}')
            ->rawColumns(['attachment', 'acciones'])
            ->make(true);
    }
}
