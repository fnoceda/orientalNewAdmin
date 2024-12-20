<ul class="navbar-nav ml-auto">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         {{__('Seleccione un idioma')}}
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{route('set_language','es')}}">Español</a>
        <a class="dropdown-item" href="{{route('set_language','en')}}">Inglés</a>
        <a class="dropdown-item" href="{{route('set_language','ko')}}">Korean</a>
        </div>
      </div>
</ul>