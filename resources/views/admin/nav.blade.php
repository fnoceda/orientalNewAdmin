<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
      <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
      <div class="input-group">
        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
          <button class="btn btn-primary" type="button">
            <i class="fas fa-search fa-sm"></i>
          </button>
        </div>
      </div>
    </form>

    <ul class="navbar-nav ml-auto">
      {{-- perfil de usuario--}}
      
      <li class="nav-item dropdown">
        
          <a href="#" class=" user-panel mt-2 pb-2 mb-2 d-flex mr-4 mb-0" data-toggle="dropdown">
            {{-- <div class="user-panel mt-2 pb-2 mb-2 d-flex"> --}}
            <span class="badge badge-secondary "><img src={{asset("/adminlte/img/user2-160x160.jpg")}} class="img-circle elevation-1 " alt="User Image"> {{auth()->user()->name}}</span> 
          </a>
          <div class="dropdown-menu dropdown-menu dropdown-menu-right">
            <a href="#" class="dropdown-item">
                <a class="text-dark" href="{{ route('logout') }}" onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </a>
          </div>
        
        
    </li>
  
      {{-- end perfil de usuario --}}
    </ul>

  </nav>