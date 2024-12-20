<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('admin.sidebar', function($view) {
            $menues = self::getMenu();
            $view->with('menues', $menues);
        });
        view()->composer('admin.notificaciones', function($view) {
            $notificaciones = $this->notificaciones();
            $view->with('notificaciones', $notificaciones);
        });
    }
    public function notificaciones(){
        $sql = " select * from articulos where existencia <= existencia_minima and es_combo is false and es_activo = true";
        $no = DB::select($sql);
        return $no;
    }
    public function getMenu(){
        $sql = " select m.id, m.name, m.url, m.padre,m.icon
                    from menus m
                    where m.activo = true
                    and m.padre is null
                    order by m.padre, m.orden ";

                session(['sucursalId' => Auth::user()->id]);
        $rs = DB::select($sql); $menues = $menu = Array();
        foreach($rs as $r){
            $menu['id'] = $r->id;
            $menu['name'] = $r->name;
            $menu['url'] = $r->url;
            $menu['icon'] = $r->icon;
            $menu['submenus'] = self::getSubMenu($r->id);
            $menues[] = $menu;
        }

        return $menues;
    }

    private function getSubMenu($menu){
        $sql = " select m.id, m.name, m.url, m.padre,m.icon
        from menus m
        join menus_perfiles mp on m.id = mp.menu
        where m.activo = true
        and padre = ".$menu."
        and mp.perfil = ". Auth::user()->perfil_id."
        order by m.padre, m.orden";
        session(['sucursalId' =>  Auth::user()->id]);
        $rs = DB::select($sql); $menues =$menu = Array();
        foreach($rs as $r){
            $menu['id'] = $r->id;
            $menu['name'] = $r->name;
            $menu['url'] = $r->url;
            $menu['icon'] = $r->icon;
            $menu['submenus'] = self::getSubMenu($r->id);
            $menues[] = $menu;
        }
        return $menues;
    }
}
