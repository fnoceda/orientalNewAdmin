<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::count();

        $widget = [
            'users' => $users,
            //...
        ];
        return view('home', compact('widget'));
    }
    public function reestar(Request $r){
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login')->withErrors("Ups usted no esta autorizado para ingresar");
    }
    public function deleteUser(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
     try { 
        $up=DB::table('users')->where('delete_contra',$r->key)->update([
            'deleted_at'=> 'now()',
            'deleted_by'=>1,
        ]);

     } catch (\Throwable $th) {
     Log::error('Error'.$th->getMessage());
     }
     return Redirect::to("https://oriental.soluciones.dev");
    }
}
