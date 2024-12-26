<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UtilidadesController;
use App\Models\User as User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerfilValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::find(Auth::id()); // Carga fresca desde la base de datos
            Log::info($user);
            if ($user && $user->perfil == 'cliente') {
                return redirect('/resstar');
            }
        }
        $vowels = array("http://localhost:8000", "https://oriental.soluciones.dev");
        $onlyconsonants = str_replace($vowels, "", $request->url());
        $es_abm=UtilidadesController::strContains($onlyconsonants,'abms');
        if($es_abm === true){$onlyconsonants.='/';}
        $ruta_principal=DB::table('menus')->select('id')->where('url',$onlyconsonants)->first();
        if(!isset($ruta_principal->id)){ #significa que son rutas de consulta o de posteo
            return $next($request);
        }else{ #si existe preguntamos si tiene permiso de consultar
           $permiso= DB::table('menus_perfiles')->where('menu',$ruta_principal->id)
        //    ->where('perfil',Auth()->user()->perfil_id)
           ->first();
           if(isset($permiso->perfil)){
                return $next($request);
           }else{
            return redirect('home');

           }
        }
        return redirect('home');
    }
}
