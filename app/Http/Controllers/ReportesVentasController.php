<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\DataTables;


class ReportesVentasController extends Controller
{
    public function index(){
        Log::info(__FILE__.'/'.__FUNCTION__);
        try {  
            $clientes=DB::table('users')->where('perfil','cliente')->get();
           return view('ventas.reportes.index',compact(['clientes']));
        } catch (\Throwable $th) {
           Log::error("error".$th->getMessage());
        }
    }
    public function data(Request $r){
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r); DB::enableQueryLog();
        $desde = ( empty($r->desde) ) ? date('Y-m-d') : $r->desde;
        $hasta = ( empty($r->hasta) ) ? date('Y-m-d') : $r->hasta;
        $cliente = (empty($r->cliente_id)) ? null: $r->cliente_id; 
        $estado= empty($r->estado) ? "estado in ('pendiente','entregado','cancelado')" : "estado ='".$r->estado."'";
            $ventas =DB::table('ventas')
            ->join('users', 'users.id', '=', 'ventas.cliente_id')
            ->leftjoin('ciudades', 'ciudades.id', '=', 'ventas.ciudad_id')
            ->leftjoin('barrios', 'barrios.id', '=', 'ventas.barrio_id')
            ->select(
                'ventas.id',
                'ventas.fecha',
                'ventas.modo',
                'ventas.estado',
                'ventas.direccion',
                'ventas.importe'
            )
            ->selectRaw('ciudades.name as ciudad')
            ->selectRaw('barrios.name as barrio')
            ->selectRaw('users.name as cliente')
            ->whereBetween('ventas.fecha', [$desde.' 00:00:01', $hasta.' 23:59:59'])
            ->whereRaw($estado)
            ->when($cliente, function($query, $cliente){
                return $query->where('ventas.cliente_id', '=', $cliente);
            })
           ->orderBy('ventas.id')->get();

            Log::info(DB::getQueryLog());
            foreach($ventas as $venta){
                $venta->detalles = DB::table('ventas_detalles')
                ->join('articulos', 'articulos.id', '=', 'ventas_detalles.articulo_id')
                ->select(
                    'ventas_detalles.cantidad',
                    'ventas_detalles.precio',
                    'ventas_detalles.total', 
                    'articulos.name'
                 )
                ->where('ventas_detalles.venta_id', '=', $venta->id)
                ->get();
                
            }
        return DataTables::of($ventas)->addColumn('acciones', function ($rs) {
                $botones = '';
                // $botones .= '<a href="'.route('admin.vender.edit', ['id'=>$rs->id]).'"  class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-edit"></i></a>';
                // $botones .= '<a href="'.route('admin.ventaaas.edit', ['id'=>$rs->id]).'"  class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit"></i></a>';
                // if ($rs->concluido == true) {
                //     $botones .= '<button class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Ya no es posible eliminar" disabled ><i class="fas fa-trash-alt"></i></button>';
                //     $botones .= '<button  class="btn btn-info btn-sm"   title="Ya no es posible asociar" disabled><i class="fa fa-users" aria-hidden="true"></i></button>';

                // }else{     
                //     $botones .= '<button onclick="eliminarme('.$rs->id.', \' '.csrf_token().' \',  \''.route('admin.cuenta.delete',['id' => $rs->id]).'\'   )" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';  
                //     $botones .= '<button  class="btn btn-info btn-sm" onclick="asociarCuenta('.$rs->numero_venta.',\''.$rs->cliente.'\')"  title="Asociar Cuenta"><i class="fa fa-users" aria-hidden="true"></i></button>';
                // }
                // $botones .= '<button type="button" class="btn btn-secondary btn-sm mr-1 ml-1" data-toggle="tooltip" data-placement="top" title="Historial" onclick="historial('.$rs->cliente_id.')"><i class="fas fa-history"></i></button>';
                return $botones;
        })
        ->editColumn('id', '{!!$id!!}')
        ->rawColumns(['attachment', 'acciones'])
        ->make(true);
    }
}
