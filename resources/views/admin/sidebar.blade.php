

@php

    function getSubMenu($id, $menus){
        $html = '';

        if(empty($menus['submenus'])){
            $html .= '<div id="collapse'.$id.'" class="collapse" aria-labelledby="heading'.$id.'" data-parent="#accordionSidebar">';
                $html .= '<div class="bg-white py-2 collapse-inner rounded">';
                    $html .= '<h6 class="collapse-header">Opciones:</h6>';
                    foreach($menus as $menu){
                        $html .= '<a class="collapse-item" href="'.$menu['url'].'"> '. $menu['name'] .' </a>';
                    }
                $html .= '</div>';
            $html .= '</div>';
        }else{
            foreach($menus as $menu){
                $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'.$id.'" aria-expanded="true" aria-controls="collapse'.$id.'">';
                        $html .= '<i class="fas fa-fw '.$menu['icon'].'"></i>';
                        $html .= '<span> '.__($menu['name']).' koko </span>';
                    $html .= '</a>';

                    $html .= '<div id="collapse'.$id.'" class="collapse" aria-labelledby="heading'.$id.'" data-parent="#accordionSidebar">';
                        $html .= '<div class="bg-white py-2 collapse-inner rounded">';
                            $html .= '<h6 class="collapse-header">Custom Components:</h6>';
                            $html .= "echo".getSubMenu($menu['id'], $menu['submenus']);
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</li>';
            }
        }


        /*
        foreach($menus as $menu){
            $html .= '<li class="nav-item">';
                $html .= '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'.$id.'" aria-expanded="true" aria-controls="collapse'.$id.'">';
                    $html .= '<i class="fas fa-fw '.$menu['icon'].'"></i>';
                        $html .= '<span> '.__($menu['name']).' </span>';
                        $html .= '</a>';
                        if(!empty($menu['submenus'])){
                            $html .= '<div id="collapse'.$id.'" class="collapse" aria-labelledby="heading'.$id.'" data-parent="#accordionSidebar">';
                                $html .= '<div class="bg-white py-2 collapse-inner rounded">';
                                    $html .= '<h6 class="collapse-header">Custom Components:</h6>';
                                    //foreach($menu['submenus'] as $subMenu){ $html .= '<a class="collapse-item" href="'.$subMenu['url'].'"> '. $subMenu['name'] .' </a>'; }
                                $html .= '</div>';
                            $html .= '</div>';
                        }
                    $html .= '</li>';
        }
        */
        return $html;
    }
@endphp

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3"> Bienvenido! </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Nav::isRoute('home') }}">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('Indicadores') }}</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @foreach($menues as $menu)
        @if(empty($menu['padre']))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse{{ $menu['id'] }}" aria-expanded="true" aria-controls="collapse{{ $menu['id'] }}">
                    <i class="fas fa-fw {{ $menu['icon'] }}"></i>
                    <span> {{ __($menu['name']) }} </span>
                </a>
                @if(!empty($menu['submenus']))
                    @php
                        echo getSubMenu($menu['id'], $menu['submenus']);
                    @endphp
                @endif
            </li>
        @endif
    @endforeach


{{--     <!-- Heading -->
    <div class="sidebar-heading">
        {{ __('Settings') }}
    </div>

    <!-- Nav Item - Profile -->
    <li class="nav-item {{ Nav::isRoute('profile') }}">
        <a class="nav-link" href="{{ route('profile') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>{{ __('Profile') }}</span>
        </a>
    </li>

    <!-- Nav Item - About -->
    <li class="nav-item {{ Nav::isRoute('about') }}">
        <a class="nav-link" href="{{ route('about') }}">
            <i class="fas fa-fw fa-hands-helping"></i>
            <span>{{ __('About') }}</span>
        </a>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
