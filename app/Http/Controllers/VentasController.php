<?php

namespace App\Http\Controllers;

use App\Models\Ventas_detalles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\DataTables;

class VentasController extends Controller
{


    public function index(Request $r){
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r);

        // $data = DB::table('ventas')
        //             ->join('users', 'users.id', '=', 'ventas.cliente_id')
        //             ->leftjoin('ciudades', 'ciudades.id', '=', 'ventas.ciudad_id')
        //             ->leftjoin('barrios', 'barrios.id', '=', 'ventas.barrio_id')

        //             ->select(
        //                 'ventas.id',
        //                 'ventas.fecha',
        //                 'ventas.modo',
        //                 'ventas.estado',
        //                 'ventas.direccion',
        //                 'ventas.importe'
        //             )
        //             ->selectRaw('ciudades.name as ciudad')
        //             ->selectRaw('barrios.name as barrio')

        //             ->selectRaw('users.name as cliente')
        //             ->whereNull('ventas.deleted_at')
        //             ->where('ventas.estado', '<>', 'entregado')
        //             ->where('ventas.estado', '<>', 'cancelado')
        //             ->orderBy('id', 'desc')
        //             ->get();
        $clientes=DB::table('users')->where('perfil','cliente')->get();
        return view('ventas.index_copy',['clientes' => $clientes]);
    }

    public function edit(Request $r){

        $venta = DB::table('ventas')
        ->leftjoin('ciudades', 'ciudades.id', '=', 'ventas.ciudad_id')
        ->leftjoin('barrios', 'barrios.id', '=', 'ventas.barrio_id')

        ->select('ventas.id','entrega_programada', 'ventas.cliente_id', 'ventas.fecha', 'ventas.direccion', 'ventas.latitud', 'ventas.longitud', 'ventas.estado', 'ventas.modo', 'ventas.forma_pago', 'ventas.importe', 'ventas.created_at' , 'ventas.delivery_importe' , 'ventas.delivery_kilometros')
        ->selectRaw('ciudades.name as ciudad')
        ->selectRaw('barrios.name as barrio')

        ->where('ventas.id', '=', $r->id)->first();

        $cliente = DB::table('users')
        ->leftjoin('ciudades', 'ciudades.id', '=', 'users.ciudad_id')
        ->select('users.name', 'users.ruc', 'users.direccion', 'users.created_at', 'users.email', 'users.telefono', 'users.latitud', 'users.longitud', 'users.id')
        ->selectRaw('ciudades.name as ciudad')
        ->where('users.id', '=', $venta->cliente_id)->first();

        $detalles =
        // DB::table('ventas_detalles')
        Ventas_detalles::with('articulo.articuloimagen')
        ->join('articulos', 'articulos.id', '=', 'ventas_detalles.articulo_id')
        ->select('articulos.name', 'articulos.costo', 'ventas_detalles.cantidad')

        ->selectRaw("articulos.costo * (CASE WHEN articulos.unidad_de_medida = 'kilo'  AND ventas_detalles.cantidad  > 100 then (ventas_detalles.cantidad / 1000) else ventas_detalles.cantidad end) as costo_total")

        ->selectRaw('articulos.precio_venta as precio')
        ->selectRaw('ventas_detalles.articulo_id')
        ->selectRaw('ventas_detalles.id as deta_id')
        ->selectRaw('ventas_detalles.color')
        ->selectRaw('ventas_detalles.medida')
        ->selectRaw('ventas_detalles.sabor')

        ->selectRaw("articulos.precio_venta * (CASE WHEN articulos.unidad_de_medida = 'kilo'  AND ventas_detalles.cantidad  > 100 then (ventas_detalles.cantidad / 1000) else ventas_detalles.cantidad end) as precio_total")

        ->selectRaw(" ((articulos.precio_venta * (CASE WHEN articulos.unidad_de_medida = 'kilo'  AND ventas_detalles.cantidad  > 100 then (ventas_detalles.cantidad / 1000) else ventas_detalles.cantidad end)) - (articulos.costo * ventas_detalles.cantidad)) as utilidad  ")

        ->where('ventas_detalles.venta_id', '=', $r->id)
        ->get();
        // dd($detalles);
        $veces = DB::table('ventas')->where('cliente_id', '=', $cliente->id)->count();
        $promedio = DB::table('ventas')->where('cliente_id', '=', $cliente->id)->avg('importe');



        return view('ventas.edit', ['venta' => $venta, 'cliente' => $cliente, 'detalles' => $detalles, 'veces' => $veces, 'promedio' => $promedio]);
    }

public function destroy(Request $r){

        $updated=DB::table("ventas")
        ->where("id", '=', $r->id)
        ->update([
          'deleted_at'=> 'now()',
          'deleted_by'=> Auth::user()->id,
          ]);
        if ($updated > 0) {
           return back()->with('status', 'Eliminado con exito');
        }else{
            return back()->withErrors('Hubo un problema al querer eliminar');
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
                'ventas.importe',
                'ventas.entrega_programada',
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
                    'ventas_detalles.precio',
                    'ventas_detalles.total',
                    'articulos.name'
                 )
                 ->selectRaw("round((CASE WHEN articulos.unidad_de_medida = 'kilo' AND ventas_detalles.cantidad  > 100 then (ventas_detalles.cantidad / 1000) else ventas_detalles.cantidad end), 3)as cantidad") // kaka
                ->where('ventas_detalles.venta_id', '=', $venta->id)
                ->get();

            }
        return DataTables::of($ventas)->addColumn('acciones', function ($rs) {
                $botones = '';
                $botones .= '<a href="'.route('ventas.edit', ['id'=>$rs->id]).'"  class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-edit"></i></a>';
                if ($rs->estado == 'entregado') {
                    $botones .= '<button class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Ya no es posible eliminar" disabled ><i class="fas fa-trash-alt"></i></button>';
                }else{
                    $botones .='<a href="'.url('ventas/delete',['id'=>$rs->id]).'"  class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="far fa-trash-alt"></i></a>';
                }
                return $botones;
        })
        ->editColumn('id', '{!!$id!!}')
        ->rawColumns(['attachment', 'acciones'])
        ->make(true);
    }

public function cambiar_estado(Request $r){
    Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r);   $rta['cod_retorno'] = 500;  $rta['des_retorno'] = 'Error inesperado';
        $validator = Validator::make($r->all(), [
            'estado' => 'required',
            'venta' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $rta['des_retorno'] = ''.$messages;
        }else{
            $updated=DB::table("ventas")
          ->where("id", '=', $r->venta)
          ->update([
            'estado'=>$r->estado,
            'updated_at'=> 'now()',
            'updated_by'=> Auth::user()->id,
          ]);
          if ($updated > 0) {
              $rta['cod_retorno'] = 200;
              $rta['des_retorno'] = 'Actualizado';
        }
      }
        return $rta;
    }

}
