
function getData(ruta) {

    return new Promise(function (resolve, reject) {
        $('#sumbitButton').attr('disabled', true);
        $('#submitSpinner').addClass('spinner-border');
        $.ajax({
            url: ruta,
            type: 'GET',
            method: 'GET',
            processData: false,
            contentType: false,
            cache: false,
            success: function (rta) {
                $('#sumbitButton').attr('disabled', false);
                $('#submitSpinner').removeClass('spinner-border');
                resolve(rta);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#sumbitButton').attr('disabled', false);
            $('#submitSpinner').removeClass('spinner-border');
            var error = '';
            if (jqXHR.status === 0) {
                error = 'Not connect: Verify Network.';
            } else if (jqXHR.status == 404) {
                error = 'Requested page not found [404]';
            } else if (jqXHR.status == 500) {
                error = 'Internal Server Error [500].';
            } else if (textStatus === 'parsererror') {
                error = 'Requested JSON parse failed.';
            } else if (textStatus === 'timeout') {
                error = 'Time out error.';
            } else if (textStatus === 'abort') {
                error = 'Ajax request aborted.';
            } else {
                error = 'Uncaught Error: ' + jqXHR.responseText;
            }
            if (error != '') {

                reject(error);
            }
        });
    });
}
function postData(ruta, data) {
    console.log('postData');
    // console.log(data);

    return new Promise(function (resolve, reject) {
        $('#sumbitButton').attr('disabled', true);
        $('#submitSpinner').addClass('spinner-border');
        $.ajax({
            url: ruta,
            type: 'POST',
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (rta) {

                $('#sumbitButton').attr('disabled', false);
                $('#submitSpinner').removeClass('spinner-border');

                if (rta.cod == 200) {
                    resolve(rta);
                } else {
                    var mensaje = '';
                    if(rta.dat){ //kaka
                        $.each(rta.dat, function (key, val) {
                            mensaje += '[<b>' + key + '<b>]: '+val + '<br />';
                        });
                    }
                    reject(rta);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#sumbitButton').attr('disabled', false);
            $('#submitSpinner').removeClass('spinner-border');
            var error = '';
            if (jqXHR.status === 0) {
                error = 'Not connect: Verify Network.';
            } else if (jqXHR.status == 404) {
                error = 'Requested page not found [404]';
            } else if (jqXHR.status == 500) {
                error = 'Internal Server Error [500].';
            } else if (textStatus === 'parsererror') {
                error = 'Requested JSON parse failed.';
            } else if (textStatus === 'timeout') {
                error = 'Time out error.';
            } else if (textStatus === 'abort') {
                error = 'Ajax request aborted.';
            } else {
                error = 'Uncaught Error: ' + jqXHR.responseText;
            }
            reject(error);
        });
    });
}

function toDataTable(data,buttons = true) {
    $('#' + data.table).DataTable({
        order: [[0, "desc"]],
        responsive: true,
        autoWidth: false,
        ajax: data.ajax,
        columns: data.columns,
        language: espanis_data,
        dom: 'Bfrtip',
        buttons: (buttons) ? getButtons(data) : [],
        initComplete: function () {
            
        },
        drawCallback: function( settings ) {
            $('#tablaCrud_filter label input').on('keydown',function(e) {
                console.log( 'entra => ' + e.which );
                if ( (e.which == 78)) {
                    $('#formCrudModal').modal('show');
                }
            });

        }
    });
}
function editar( ruta ){ //kaka
    //console.log(ruta);
    $('#password').attr('placeholder', 'Solo ingrese si desea cambiar');
    $('#password').attr('required', false);

    getData(ruta).then(function(rta){
        //console.log('getData OK'); console.log(rta);
        populateForm('formCrud', JSON.parse( rta ) );
        $('#formCrudModal').modal('show');
    }).catch(function(error){
        console.log('getData dio error'); console.log(error);
        alertsAndMessages('Ocurrio un Error',error.msg,'alert-danger')

    });
}
function store(formData, ruta){
    postData(ruta, formData).then(function(rta){
        console.log('postData OK'); console.log(rta);
        $('#formCrud').trigger('reset');
        $('#formCrudModal').modal('hide');
        alertsAndMessages('Buen Trabajo! ',rta['msg'],'alert-success')
        $('#tablaCrud').DataTable().ajax.reload();
    }).catch(function(error){
        console.log('postData dio errorrrr'); console.log(error);
        alertsAndMessages('Ocurrio un Error',error.msg,'alert-danger')

    });
}
function update(formData, ruta){
    postData(ruta, formData).then(function(rta){
        console.log('postData OK'); console.log(rta);
        $('#formCrud').trigger('reset');
        $('#formCrudModal').modal('hide');
        alertsAndMessages('Buen Trabajo! ',rta['msg'],'alert-success')

        $('#tablaCrud').DataTable().ajax.reload();
    }).catch(function(error){
        console.log('postData dio errors'); console.log(error);
    });
}
function eliminar(id, _token, rutaConfirm, rutaDestroy){
    getData(rutaConfirm).then(function(rta){
        var data = JSON.parse( rta );
        data.text_delete = "Estas seguro de querer eliminar?"
        populateForm('form-delete', data );
        $('#modal_delete').modal('show');
        $(".delete").click(function() {
        var formData = new FormData(); // Currently empty
        formData.append('id', id);
        formData.append('_token', _token);
    
        postData(rutaDestroy, formData).then(function(rta){
            console.log('postData OK'); console.log(rta);
            alertsAndMessages('Buen Trabajo! ',rta['msg'],'alert-success')
            $('#modal_delete').modal('hide');
            $('#tablaCrud').DataTable().ajax.reload();
        }).catch(function(error){
            console.log('postData dio error'); console.log(error);
            alertsAndMessages('Ocurrio un Error',error.msg,'alert-danger')
    
        });
          
    });
    }).catch(function(error){
        console.log('getData dio error'); console.log(error);
        alertsAndMessages('Ocurrio un Error',error.msg,'alert-danger')
    });
}

function populateForm(formId, data,dataExtra=0) {


    $.each(data, function (key, value) {
        $('.chsucursales').attr('checked', false);
        if ($('#' + formId + ' #' + key).length > 0) {
            // console.log('No Array :: ' + key +' => '+ value);
            if( key == 'imagen'){
                if(value){
                    $('#preview').html("<img id='target' src='" + value + "' style='display: inline-block;'>");
                    var src = document.getElementById("imagen");
                    var target = document.getElementById("target");
                    showImage(src,target);
                    $('#eliminarImagen').show();
                }else{
                    $('#eliminarImagen').hide();
                }
            }else{
                if( $( '#' + formId + ' #' + key ).is(':checkbox')){ //si es checkbox comprobamos para poner como checked o no
                    if(value === true){
                        $( '#' + formId + ' #' + key ).attr('checked', true);
                    }else{
                        $( '#' + formId + ' #' + key ).attr('checked', false);
                    }
                }else{ //si es un input cualquiera asiganmos su valor
                    $('#' + formId + ' #' + key).val(value);
                }
            }
        } else {
            //  console.log(key + ' no existe por id buscamos por nombre');
            if ( $('#' + formId + ' [name="' + key + '[]"]').length > 0) {
                // console.log(key + 'existe por nombre');
                let esArray = Array.isArray(value);
                if(esArray){
                    // console.log('Es Array :: ' + key); console.log(value);
                    if( $('#' + formId + ' [name="' + key + '[]"]').is(':checkbox')){ //si es checkbox comprobamos para poner como checked o no
                        $.each(value, function (k, v) {
                            $( '#' + formId + ' #' + key + '_' + v.id ).attr('checked', true);
                        });
                    }else{ //si es un input cualquiera asiganmos su valor
                        $('#' + formId + ' #' + key).val(value);

                    }
                }
            }
        }
    });
   
   
}

function numeroAlAzar(id){
    var num_li = $('#'+id).length + 1;
    num_li = (num_li * 1000) + getRandomArbitrary(1, 100);
    return num_li
}

function getRandomArbitrary(min, max) {
    return Math.round(Math.random() * (max - min) + min);
}
function alertsAndMessages(title,subtitle='',tipe_alert){
   
    const  message='<div class="alert '+tipe_alert+' "><strong>'+title+'!</strong>'+subtitle+'</div>';
    //modales
    $('#alert_message_type').empty().append(message).collapse({toggle: false}).collapse('show');
    setTimeout(function(){
      $('#alert_message_type').collapse({toggle: false}).collapse('hide');
    },5000);
   
  }
  var espanis_data = {
    "processing": "Procesando...",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "infoThousands": ",",
    "loadingRecords": "Cargando...",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    },
    "aria": {
        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad",
        "collection": "Colección",
        "colvisRestore": "Restaurar visibilidad",
        "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
        "copySuccess": {
            "1": "Copiada 1 fila al portapapeles",
            "_": "Copiadas %d fila al portapapeles"
        },
        "copyTitle": "Copiar al portapapeles",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Mostrar todas las filas",
            "1": "Mostrar 1 fila",
            "_": "Mostrar %d filas"
        },
        "pdf": "PDF",
        "print": "Imprimir"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Rellene todas las celdas con <i>%d<\/i>",
        "fillHorizontal": "Rellenar celdas horizontalmente",
        "fillVertical": "Rellenar celdas verticalmentemente"
    },
    "decimal": ",",
    "searchBuilder": {
        "add": "Añadir condición",
        "button": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "clearAll": "Borrar todo",
        "condition": "Condición",
        "conditions": {
            "date": {
                "after": "Despues",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual a",
                "notBetween": "No entre",
                "notEmpty": "No Vacio",
                "not": "Diferente de"
            },
            "number": {
                "between": "Entre",
                "empty": "Vacio",
                "equals": "Igual a",
                "gt": "Mayor a",
                "gte": "Mayor o igual a",
                "lt": "Menor que",
                "lte": "Menor o igual que",
                "notBetween": "No entre",
                "notEmpty": "No vacío",
                "not": "Diferente de"
            },
            "string": {
                "contains": "Contiene",
                "empty": "Vacío",
                "endsWith": "Termina en",
                "equals": "Igual a",
                "notEmpty": "No Vacio",
                "startsWith": "Empieza con",
                "not": "Diferente de"
            },
            "array": {
                "not": "Diferente de",
                "equals": "Igual",
                "empty": "Vacío",
                "contains": "Contiene",
                "notEmpty": "No Vacío",
                "without": "Sin"
            }
        },
        "data": "Data",
        "deleteTitle": "Eliminar regla de filtrado",
        "leftTitle": "Criterios anulados",
        "logicAnd": "Y",
        "logicOr": "O",
        "rightTitle": "Criterios de sangría",
        "title": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "value": "Valor"
    },
    "searchPanes": {
        "clearMessage": "Borrar todo",
        "collapse": {
            "0": "Paneles de búsqueda",
            "_": "Paneles de búsqueda (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Sin paneles de búsqueda",
        "loadMessage": "Cargando paneles de búsqueda",
        "title": "Filtros Activos - %d"
    },
    "select": {
        "1": "%d fila seleccionada",
        "_": "%d filas seleccionadas",
        "cells": {
            "1": "1 celda seleccionada",
            "_": "$d celdas seleccionadas"
        },
        "columns": {
            "1": "1 columna seleccionada",
            "_": "%d columnas seleccionadas"
        },
        "rows": {
            "1": "1 fila seleccionada",
            "_": "%d filas seleccionadas"
        }
    },
    "thousands": ".",
    "datetime": {
        "previous": "Anterior",
        "next": "Proximo",
        "hours": "Horas",
        "minutes": "Minutos",
        "seconds": "Segundos",
        "unknown": "-",
        "amPm": [
            "am",
            "pm"
        ]
    },
    "editor": {
        "close": "Cerrar",
        "create": {
            "button": "Nuevo",
            "title": "Crear Nuevo Registro",
            "submit": "Crear"
        },
        "edit": {
            "button": "Editar",
            "title": "Editar Registro",
            "submit": "Actualizar"
        },
        "remove": {
            "button": "Eliminar",
            "title": "Eliminar Registro",
            "submit": "Eliminar",
            "confirm": {
                "_": "¿Está seguro que desea eliminar %d filas?",
                "1": "¿Está seguro que desea eliminar 1 fila?"
            }
        },
        "error": {
            "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
        },
        "multi": {
            "title": "Múltiples Valores",
            "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
            "restore": "Deshacer Cambios",
            "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
        }
    },
    "info": "Mostrando de _START_ a _END_ de _TOTAL_ entradas"
};

function getButtons(data) {
    var buttons = [{
            extend: 'pageLength',

        },
        {
            extend: 'copy',
            text: '<i class="fas fa-file-alt"></i>',
            titleAttr: 'Copiar a Porta Papeles',
            exportOptions: {
                columns: 'th:not(.notexport)'
            }
        },
        {
            extend: 'print',
            text: '<i class="fas fa-print"></i>',
            titleAttr: 'Imprimir',
            exportOptions: {
                columns: 'th:not(.notexport)'
            }
        },
        {
            extend: 'excel',
            text: '<i class="fas fa-file-excel"></i>',
            messageTop: data.topMsg,
            messageBottom: data.footerMsg,
            filename: data.filename,
            title: data.title,
            titleAttr: 'Descargar en Excel',
            exportOptions: {
                columns: 'th:not(.notexport)'
            }
        },
        {
            extend: 'pdf',
            text: '<i class="fas fa-file-pdf"></i>',
            messageTop: data.topMsg,
            messageBottom: '\n' + data.footerMsg,
            filename: data.filename,
            title: data.title,
            titleAttr: 'Descargar en PDF',
            exportOptions: {
                columns: 'th:not(.notexport)'
            },
            customize: function (doc) {
                var colCount = new Array();
                $('#' + data.table).find('tbody tr:first-child td').each(function () {
                    if ($(this).attr('colspan')) {
                        for (var i = 1; i <= $(this).attr('colspan'); $i++) {
                            colCount.push('*');
                        }
                    } else {
                        colCount.push('*');
                    }
                });
                doc.content[1].table.widths = colCount;
            }
        },
        {
            text: 'Nuevo',
            titleAttr: 'Agregar uno Nuevo',
            action: function (e, dt, node, config) {
                $('#password').attr('placeholder', '');
                $('#password').attr('required', true);
                $('#id').val('');
                $('#formCrud').trigger('reset');
                $('#formCrudModal').modal('show');
                $('#formCrudModal').on('shown.bs.modal', function () {
                    $('#formCrud input:text:visible:first', this).focus();
                });
             
              
            }
        },
    ];
    return buttons;
}