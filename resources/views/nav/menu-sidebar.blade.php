{{-- el querry del menu es lanzado desde el archivo app/providers appserviceprovider.php para tener en cuenta
    las tablas productos y categorias estan solo de prueba --}}
    @php

    function inObject($val, $obj){
        foreach ($obj as $ob) { if ( $ob->padre == $val ) { return true; } }
        return false;
    }

@endphp
    <ul class="nav nav-pills nav-sidebar flex-column"  role="menu" data-widget="treeview">
        @foreach($menues as $menu)
            @if(is_null($menu->padre))
                @if( !inObject($menu->id, $menues) )
                    <li ><a  href="{{ url($menu->url) }}" ><i class='fa fa-link'></i> <span>{{ $menu->name }}</span></a></li>
                @else

                    <li class="nav-item has-treeview ">

 <a href="{{ url($menu->url) }}" class="nav-link active bg-purple" ><i class="{{ $menu->icon }}"></i> <span>{{ $menu->name }}</span> <i class="fa fa-angle-down pull-right"></i></a>

                            <ul class="nav nav-treeview">

                                    @foreach($menues as $subMenu)

                                            @if ($subMenu->padre == $menu->id)


                                            <li class="nav nav-pills nav-sidebar flex-column" role="menu"  data-widget="treeview">
                                                <li class="nav-item has-treeview menu-open">


                                                    <a href="{{ url($subMenu->url) }}" class="nav-link bg-light" >{{ $subMenu->name }}</a>
                                                    {{-- <ul class="nav nav-treeview">
                                                        @foreach($menues as $subsubMenu)
                                                            @if ($subsubMenu->submenu == $subMenu->id)

                                                                <ul ><a href="{{ url($subsubMenu->url) }}" class="bg-primary">{{ $subsubMenu->name }}</a></ul>

                                                            @endif
                                                        @endforeach
                                                    </ul> --}}

                                                </li>
                                            </li>
                                            @endif
                                    @endforeach
                            </ul>
                    </li>

                @endif
            @endif
        @endforeach
    </ul>
    {{-- <li class="nav-item has-treeview">
                        <a href="{{ url($menu->url) }}" class="nav-link active"><i class="{{ $menu->icon }}"></i> <span>{{ $menu->name }}</span> <i class="fa fa-angle-down pull-right"></i></a>
                        <ul class="nav nav-treeview">
                            @foreach($menues as $subMenu)
                                    @if($subMenu->padre == $menu->id)
                                        <li><a href="{{ url($subMenu->url) }}">{{ $subMenu->name }}</a></li>
                                    @endif
                            @endforeach
                        </ul>
                    </li>    --}}
