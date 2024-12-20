@extends('layouts.admin')

@section('main-content')
<div class="col-sm-12" style="padding: 0; float: left;">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <a href="#" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-primary">
        + Crear Categorias / Sub Categorias
      </a>
    </div>
  </div>
</div>

<div class="col-sm-6" style="padding: 0 5px 0 0; float: left;">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h5 style="margin: 0;">Categorias</h5>
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Error!</strong> Revise los campos obligatorios.<br><br>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    @if(Session::has('success'))
    <div class="alert alert-info">
          {{Session::get('success')}}
    </div>
    @endif
    <div class="card-body">
      <div class="table table-sm">
        <table id="crud-table" class="table table-striped table-bordered tablas">
          <thead>
            <tr>
              <td>#</td>
              <td>Nombre</td>
              <td>이름</td>
              <td>Icono</td>
              <td>Acciones</td>
            </tr>
          </thead>
          <tbody>
            @foreach($categorias as $cat)
            @if($cat->padre == null)
            <tr>
              <td>{{$cat->id}}</td>
              <td>{{$cat->name}}</td>
              <td>{{$cat->name_co}}</td>
              <td style="width: 50px; padding: 0; text-align: center;">
                @foreach($iconos as $icon)
                @if($cat->icono_id == $icon->id)
                  <img src="/{{$icon->path}}" style="width: 40px;"/>
                @endif
                @endforeach
              </td>
              <td style="width: 110px; text-align: center;">
                <a href="#" class="btn btn-info btn-sm" style="border-radius: 50px;" title="Ver">
                  <i class="fas fa-search"></i>
                </a>
                <a href="#" class="btn btn-warning btn-sm" style="margin-left: 5px; margin-right: 5px; border-radius: 50px;" title="Modificar">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-sm" style="border-radius: 50px;" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            @endif
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<div class="col-sm-6" style="padding: 0 0 0 5px; float: left;">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h5 style="margin: 0;">Sub Categorias</h5>
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Error!</strong> Revise los campos obligatorios.<br><br>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    @if(Session::has('success'))
    <div class="alert alert-info">
          {{Session::get('success')}}
    </div>
    @endif
    <div class="card-body">
      <div class="table table-sm">
        <table id="crud-table" class="table table-striped table-bordered tablas">
          <thead>
            <tr>
              <td>#</td>
              <td>Nombre</td>
              <td>이름</td>
              <td>Categoría</td>
              <td>Icono</td>
              <td>Acciones</td>
            </tr>
          </thead>
          <tbody>
            @foreach($categorias as $cat)
            @if($cat->padre == !null)
            <tr>
              <td>{{$cat->id}}</td>
              <td>{{$cat->name}}</td>
              <td>{{$cat->name_co}}</td>
              <td>
                {{$cat->padre}}
              </td>
              <td style="width: 50px; padding: 0; text-align: center;">
                @foreach($iconos as $icon)
                @if($cat->icono_id == $icon->id)
                  <img src="/{{$icon->path}}" style="width: 40px;"/>
                @endif
                @endforeach
              </td>
              <td style="width: 110px; text-align: center;">
                <a href="#" class="btn btn-info btn-sm" style="border-radius: 50px;" title="Ver">
                  <i class="fas fa-search"></i>
                </a>
                <a href="#" class="btn btn-warning btn-sm" style="margin-left: 5px; margin-right: 5px; border-radius: 50px;" title="Modificar">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-sm" style="border-radius: 50px;" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            @endif
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<style>
</style>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Categorias</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('categorias.agregar') }}" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group">
            <label for="nombre_categoria">Nombre</label>
            <input type="text" class="form-control" name="nombre_categoria" placeholder="Ej. Ropas">
          </div>
          <div class="form-group">
            <label for="nombre_co_categoria">Nombre 이름</label>
            <input type="text" class="form-control" name="nombre_co_categoria" placeholder="Ej. 옷">
          </div>
          <div class="form-group">
            <label for="categoria_subcategoria">Categoría</label>
            <select class="form-control" name="categoria_subcategoria">
              <option value="nulo">Seleccione..</option>
              @foreach($categorias as $cat)
              @if($cat->padre == null)
              <option value="{{$cat->id}}">{{$cat->name}}</option>
              @endif
              @endforeach
            </select>
            <small id="emailHelp" class="form-text text-muted">Completar si es una sub categoría.</small>
          </div>
          <div class="custom-file" style="margin-top: 5px;">
            <input type="file" class="custom-file-input" id="file" name="file" required>
            <label class="custom-file-label" for="file">Seleccionar un icono</label>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group" style="margin-top: 20px;">
            <div id="file-preview-zone" style="width: 80px;">

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" onclick="agregar_categoria()">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  #file-preview {width: 100%; padding: 5px; border: 1px solid #e5e5e5;}
</style>

@endsection

@section('script')
<script type="text/javascript">
  $('.tablas').DataTable();

  function readFile(input) {
   if (input.files && input.files[0]) {
     var reader = new FileReader();

     reader.onload = function (e) {
       var filePreview = document.createElement('img');
           filePreview.id = 'file-preview';
           //e.target.result contents the base64 data from the image uploaded
           filePreview.src = e.target.result;
           console.log(e.target.result);

           var previewZone = document.getElementById('file-preview-zone');
           previewZone.appendChild(filePreview);
     }
     reader.readAsDataURL(input.files[0]);
     }
   }

   var fileUpload = document.getElementById('file');
       fileUpload.onchange = function (e) {
       readFile(e.srcElement);
   }


</script>



@endsection
