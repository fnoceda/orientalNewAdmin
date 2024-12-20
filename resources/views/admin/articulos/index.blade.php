@extends('layouts.admin')

@section('main-content')
<div id='errores_ventas' style='display:none' class='alert alert-danger' ></div>
<div id='success' style='display:none;' style="background: #4e73df  !important;" class='alert alert-primary' ></div>
<input  class="btn button " type="hidden" id="mostrar_errores" value="1" disabled error="">

<div class="modal fade" id="eliminame" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
        <div id='myModalMsg'><h4 id="delete_yes_not"></h4></div>
        <div id="myModalSendForm" style="display:none"></div>
      </div>
      <div class="modal-footer">

          <p class="m float-right">Podria afectar a otras categorias</p>
        <button type="button" class="btn btn-primary" data-dismiss="modal">NO</button>
        <a href="#"  class="btn btn-danger" id="eliminame_a">Si</a>
      </div>
    </div>
  </div>
</div>

<div class="form form-inline" >


  <div class="col col-6 ">
    <div id='jstree-tree'>
      <div id="jstree-result" class="col-sm-6">this is result</div>
      <h4 id="delete_yes_not"></h4>
    </div>
  </div>
  <div class="ml-6 float-right col-5 ">
    <div class="container py-7">
      <div class="row">
        <div class="col-md-8">
          <div class="row">
            <div class="col-md-12 mx-auto ">

              <!-- form card login -->
              <div class="card">
                <div class="card-header" id="mostrar_estado">
                  <p>Agregar Categoria Principal</p>
                </div>
                <div class="card-body">

                  <div class="form-group">

                    <input type="hidden" name="id_categoria" id="id_categoria" required value="sumar">
                    <input type="text"  class="form-control form-control-lg rounded-0" id="name_categoria" name="name_categoria" placeholder="Nombre Español">
                    <input type="text"  class="form-control form-control-lg rounded-0" id="name_categoria_co" name="name_categoria_co" placeholder="Nombre Coreano">
                    <div class="form-group">
                      <select class="form-control col-form-label-lg rounded-0" id="exampleFormControlSelect1" style="width: 255px">
                        <option value="0">seleccione el icono</option>
                        @foreach ($iconos as $i)
                      <option value="{{$i->id}}"><img src="" alt="">{{$i->name}}</option>
                        @endforeach
                      </select>
                     
                    </div>
                    <div id='errores_modal' style='display:none' class='alert alert-danger'></div>
                  </div>
                  <div class="container ">
                  <button class="btn btn-success col-5 ml-2 mt-2 mr-0 float-right" id="guardar_categoria">Guardar</button>
                <form action="#"  id="from_env" method="GET">
                    @csrf
                    <button title="Eliminar"  class="btn btn-danger mt-2 btn-sm py-2 col-4 " type="btn" data-toggle="modal" data-target="#eliminame" class="btn btn-danger btn-lg float-right" data-titulo="" data-mensaje="" id="eliminar" disabled>Eliminar</button>
                </form>
              </div>
                </div>
                <div class="card-body">
                    <div class="container border" >
                      <img id="img_muestra" src="{{ asset('img/favicon.png') }}" class="rounded-circle" alt="user-image" width="80px" height="70px" >
                    </div>
                </div>
                <!--/card-block-->
                
              </div>
              <!-- /form card login -->
            </div>
          </div>
          <!--/row-->
        </div>
        <!--/col-->
      </div>
      <!--/row-->
    </div>
    <!--/container-->

  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  $( document ).ready(function() {
@php
  echo ' var categorias = '.json_encode($categorias).'; ';
  echo ' var iconos = '.json_encode($iconos).'; ';
@endphp
$( "#mostrar_errores" ).click(function() {
    var algo= $("#mostrar_errores").val();
    var tipo_error= $("#mostrar_errores").attr('error');
    if (algo == 1) {
        var html = '';
            html += '<strong>Error!'+tipo_error+'</strong><br><br>';
            $('#errores_ventas').html(html).promise().done(function(){
                $(this).slideToggle('');
                setTimeout(function(){ $('#errores_ventas').css('display','none'); }, 5000);
            });   

    }else{
        var html = '';
                    html += '<strong>Inventario Actualizado </strong><br><br>';
                    $('#success').html(html).promise().done(function(){
                        $(this).slideToggle();
                        setTimeout(function(){ $('#success').css('display','none'); }, 5000);
                    });
    }
});
reiniciar_jstree();
function reiniciar_jstree(){
  $('#jstree-tree').jstree("destroy").promise().done(function(){
        $('#jstree-tree').on('changed.jstree', function (e, data) {
        var objNode = data.instance.get_node(data.selected);
        $('#jstree-result').html('Selected: <br/><strong>' + objNode.id+'-'+objNode.text+'</strong>');
        }).jstree({
            core: {
              data:categorias
            }
          }).promise().done(function(){
                $('#jstree-tree').on("changed.jstree", function (e, data) {
                $( "#name_categoria" ).focus();
                console.log(data);
                recuadro(data.node)
          });
      });            
  });
}
function recuadro(data_node){
       
       $( "#mostrar_estado" ).html(" ");
       $( "#mostrar_estado" ).html("<h5> Agregar en "+data_node.text+"</h5>");
       $( "#id_categoria" ).attr("value"," ");
       $( "#id_categoria" ).attr("value",data_node.id);

       if (data_node.id != "sumar") {
            $("#eliminar").prop( "disabled", false );
        }else{
            $("#eliminar").prop( "disabled", true );
        }

        $( "#eliminar" ).attr("data-mensaje"," ");
        $( "#eliminar" ).attr("data-mensaje","Estas seguro de eliminar la categoria "+data_node.text);
        $( "#eliminar" ).attr("href"," ");
        var e = "";
        var e = document.createElement('a');
        var linkText = document.createTextNode("redireccion");
        e.appendChild(linkText);
        e.href = "/categoria/articulos/eliminar/"+data_node.id;
        console.log(e.href);
        $( "#eliminame_a" ).attr("href"," ");
        $( "#eliminame_a" ).attr("href",e.href);
   }
   $('#guardar_categoria').click(function(e){
        e.preventDefault();//no borrar este hace la magia
        e.stopImmediatePropagation();//este tampoco
        var name_categoria = $('#name_categoria').val();
        var name_categoria_co = $('#name_categoria_co').val();
        // var icon = $('#icon').val();//lo que se escribe
        var icon = null;
        if (($('#exampleFormControlSelect1').val()) == 0) {
          icon = 1;
        }else{
          icon=$('#exampleFormControlSelect1').val();
        }
        var id_padre = $('#id_categoria').val();
        console.log(name_categoria,name_categoria_co,icon,id_padre);

        $.ajax({
        type:"GET",
        url: '{!! url("/categorias/articulos/guardar") !!}',
        type: 'GET',
        data: { 
          name_categoria : name_categoria,
          name_categoria_co : name_categoria_co,
          icon: icon,
          id_padre: id_padre,
            },

        success: function(rta){
          console.log(rta);
          if (rta.cod_retorno > 200) {
                $("#mostrar_errores").attr('value',1);
                $("#mostrar_errores").attr('error',rta.des_retorno);
                $("#mostrar_errores").click();
          }else{
                $('#name_categoria').val('');
                $('#name_categoria_co').val('');
                $('#icon').val('');
                $("#exampleFormControlSelect1 option[value=0]").attr("selected",true);
                $('#id_categoria').attr('value','sumar');
                $('#mostrar_estado').html('<p>Agregar Categoria Principal</p>');
                categorias=rta.select;
                setTimeout(function(){ reiniciar_jstree(); }, 1000);
          }

        }
      }).fail( function( jqXHR, textStatus, errorThrown ) {
            if (jqXHR.status === 0) {
                console.log('Not connect: Verify Network.');
            } else if (jqXHR.status == 404) {
                console.log('Requested page not found [404]');
            } else if (jqXHR.status == 500) {
                console.log('Internal Server Error [500].');
            } else if (textStatus === 'parsererror') {
                console.log('Requested JSON parse failed.');
            } else if (textStatus === 'timeout') {
                console.log('Time out error.');
            } else if (textStatus === 'abort') {
                console.log('Ajax request aborted.');
            } else {
                console.log('Uncaught Error: ' + jqXHR.responseText);
            }
        }); 

});
$("#exampleFormControlSelect1").change(function() {
  if (($(this).val()) !=0 ) {
          iconos.forEach(function callback(ele, index, array) {
                if(ele.id == ($("#exampleFormControlSelect1").val()) ){
                  var html= '{!!asset("storage/img/'+ele.path+'")!!}'
                  console.log(ele.path);
                $('#img_muestra').attr('src',html);               
                }
            });
  }

})

$('#eliminame').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var form = button.data('form') // Extract info from data-* attributes
  var titulo = button.data('titulo') // Extract info from data-* attributes
  var mensaje = button.data('mensaje') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

  var modal = $(this);
  modal.find('#myModalLabel').text(titulo);
  modal.find('#myModalSendForm').html(form);
  modal.find('#myModalMsg').html(mensaje);

    
});

});//documen ready



</script>

@endsection