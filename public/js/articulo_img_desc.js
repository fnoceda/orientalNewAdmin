
//logica de imagenes y descripcion aprhil 2024

function descripcion(ruta){
    $('#img_example').empty()
    getData(ruta).then(function(rta){
        $('#art_des_id').val(rta.id)
            if(rta.dta.length > 0) {populateImg(rta.dta)}
        else{
            $('#modal_art_des_img').modal('show');
        }
        console.log(rta);
    }).catch(function(error){
        console.log('getData dio error'); console.log(error);
        alertsAndMessages('Error',error.msg,'alert-danger')

    });
}

function populateImg(images){
    
    images.map(function(x) {
        var ele={};
        ele.path = url_images+"/"+x.path ?? no_image;
        ele.descripcion = x.descripcion ?? '';
        ele.identificador=x.id
        ele.path_name = x.path;
        ele.orden = x.orden;
        ele.descripcion_co = x.descripcion_co ?? '';
        $('#img_example').append([ele].map(ItemImages).join(''));
    });
    $('#modal_art_des_img').modal('show');
    
}

$(document).on("change", ".img-val", function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    validar_img($(this).attr('id'))
});


function validar_img(id) {

  var o = document.getElementById(id);
  var foto = o.files[0];
  if (o.files.length == 0 || !(/\.(jpg|png|jpeg)$/i).test(foto.name)) {
    alert('Ingrese una imagen con alguno de los siguientes formatos: .jpeg/.jpg/.png.');
    return false;
  }

  var img = new Image();
  img.onload = function dimension() {
    // if (this.width.toFixed(0) <= 300 && this.height.toFixed(0) <= 300) {
        var num= document.getElementById('file_'+id.split('_')[2] ?? 1);
        var url=  URL.createObjectURL(foto);
        num.src = url;
        $('#manipulado_'+id.split('_')[2]).val(true);
        ordenar_por_id_2()
        return false;
    // } else {
    //     $('#'+id).val('')
    //     console.log($('#'+id).val());
    //     var num= document.getElementById('file_'+id.split('_')[2] ?? 1);
    //     num.src = no_image;
    //     alert('Las medidas deben ser menor o igual a: 300 x 300');
    // }
  };
  img.src = URL.createObjectURL(foto);

  
}

function deleteDes(id,identificador){
    if(identificador == 'no-id'){
        $('#delete_img_des_'+id).remove();
        ordenar_por_id_2()
    }else{
        var formData = new FormData();
        formData.append('id', identificador);
        formData.append('_token', token);
        formData.append('articulo_id', $('#art_des_id').val());
        postData(url_delete, formData).then(function(rta){
            $('#delete_img_des_'+id).remove();
            ordenar_por_id_2()
            alertsAndMessages('Eliminado ',rta.msg,'alert-success')
        }).catch(function(error){
            console.log('postData dio error'); console.log(error);
            alertsAndMessages('Error',error.msg,'alert-danger')
        });
    }
    

}

function validarFormImg(){
    $('#guardar_art_des_img').prop('disabled',true);
    var errors=1;
    var error_message= 'Debe agregar por los menos una imagen y una descripción';
    $('#img_example .validar').each(function(e,data) {
        if(e == 0){error_message = ''; errors=0;}
        if($(this).attr('type') == 'file'){
            var modificado=$('#manipulado_'+$(this).attr('id')).val();
            var o = document.getElementById($(this).attr('id'))
            var foto = o.files[0];
            if(modificado){
                if (o.files.length == 0 || !(/\.(jpg|png|jpeg)$/i).test(foto.name)) {
                    error_message ='\n falta una imagen con alguno de los siguientes formatos: .jpeg/.jpg/.png.';
                    errors=1;
                }
            }
        }else{
            // if($(this).val().length <= 5){
            //     error_message ='\n Debe agregar una descripcion por lo menos de una palabra de 5 letras';
            //     errors=1;
            // }
        }
    });
    if(errors == 1){
        $('#guardar_art_des_img').prop('disabled',false);
        return  alertsAndMessages('Atencion.! ',error_message,'alert-info')
    }else{
        guardarImagenYdescripcion()
    }
}

function guardarImagenYdescripcion(){
    var formData = new FormData($('#form-art_des_img')[0]);
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
            type:"POST",
            url: url_safe,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(rta){
                $('#guardar_art_des_img').prop('disabled',false);

              if (rta.cod == 200) {
                    alertsAndMessages('',rta.msg,'alert-success')
                    setTimeout(function(){ $('#modal_art_des_img').modal('hide'); }, 2000);

                  }else{
                    alertsAndMessages('error',rta.msg,'alert-danger')
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
    }




  function ordenar_por_id_2(){
    console.log("orden")
    var orden_nuevo = 1;
    $('#img_example .bt-orden').each(function(e,data) {
        $(this).html(orden_nuevo);
        orden_nuevo = orden_nuevo + 1;      
    })
  }

  $("#add_img_des").click(function() {
    var ele={};
    ele.path = no_image
    ele.identificador='no-id'
    $('#img_example').append([ele].map(ItemImages).join(''));
    ordenar_por_id_2()
  });

  const ItemImages = ({ num=numeroAlAzar('form-art_des_img #img_example'),path,descripcion='',disabled='dasabled',identificador,orden=ordenf('form-art_des_img #img_example .img'),path_name='',descripcion_co=''}) =>
  `
  <div class="row-12" id="delete_img_des_${num}">
      <div class="form-row">
          <div class="col-7">
              <div class="mb-1" >
                  <label class="custom-file-label" for="file">Seleccionar una imagen</label>
                  <input type="hidden"  id="manipulado_${num}" name="art_img[${num}][manipulado]" value="false">
                  <input type="hidden"  id="path_name_${num}" name="art_img[${num}][path_name]" value="${path_name}">
                  <input type="hidden"  id="identificador_${num}" name="art_img[${num}][identificador]" value="${identificador}">
                  
                  <input type="file" class="custom-file-input img-val  validar" id="file_img_${num}" name="art_img[${num}][file_img]" numero='${num}'>
              </div>
              <div class="mt-1"> 
                  <textarea class="form-control validar" maxlength="500" name="art_img[${num}][descripcion]" id="art_desc" rows="3">${descripcion}</textarea>
              </div>
              <div class="mt-1"> 
                  <textarea class="form-control" maxlength="500" name="art_img[${num}][descripcion_co]" id="art_desc_co" rows="3">${descripcion_co}</textarea>
              </div>
          </div>
          <div class="col-3 border">
            <img src="${path}"  width="170px" height="150px" id="file_${num}">
          </div>
          <div class="col-1">
              <button type="button" class="btn btn-danger" onclick="deleteDes(${num},'${identificador}')" id="delete_img_des" value="${num}">
                  <i class="fas fa-trash" type="button"></i>
              </button>
              <button type="button" class="btn btn-primary mt-1 bt-orden" disabled  >
                  ${orden}
              </button>
          </div>
      </div>
      <hr/>
  </div>
  
  `;

function ordenf(id){
    var num_li = $('#'+id).length + 1;
    return num_li;
}
