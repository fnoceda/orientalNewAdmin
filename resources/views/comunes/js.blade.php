<script>

	//este si es el archivo comunes!!

function cambiarSucursal(id){


	$.ajax({
		type:"get",
		url: '/configuracion/cambiar_sucursal/'+id,
		data:{'id':id},
		success:function(i){
			console.log(i);
			if(i == 1){
				history.go(0); // esto recarga la pagina
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

function validarForm(form){

        var errores = ''; $('#errores').hide('fast');
        var formuId = $(form).attr('id'); console.log(formuId);
        var elementos = $('#'+formuId).find('textarea,select,input:not(:button, :submit, :reset, :file)');

        elementos.each(function(){
            if(this.name != ''){
                var label = $('label[for="' + this.name + '"]').html();
                if( ((this.type == 'text') || (this.type == 'select-one') || (this.type == 'textarea'))){
                    if( $(this).hasClass('required') && vacio($(this).val())){
                        errores += '<li>El campo '+label+' es requerido, debe ingresar un valor</li>';
                    }
                    if( $(this).hasClass('nozero') ){
						console.log(0)
						if( trim($(this).val()) == 0 ){
							console.log(0)
							errores += '<li>El campo '+label+' debe tener valor mayor a cero</li>';
						}else{
							console.log('no zero');
						}
                    }else{
						console.log('no tiene la clase');
					}
                }else if( this.type == 'radio' ){
                    if( $(this).hasClass('required') && ( ! $('#'+formuId+' input[name="'+this.name+'"]:radio').is(':checked')  )){
                        //console.log('No eligio nada para '+this.name);
                        //el bucle pasa por cada input asi que pasa N veces por cada radio, checkbox
                        //por eso validamos, para no repetir el mensaje
                        if( errores.indexOf('<li>El campo '+label+' es requerido, debe elegir una opcion</li>') == -1 ){
                            errores += '<li>El campo '+label+' es requerido, debe elegir una opcion</li>';
                        }

                    }
                }
            }
        });

		if( formuId == 'timbrados' ){
            if((trim($('#nro_timbrado').val()) == '') && (trim($('#fecha_desde_timbrado').val()) == '')&& (trim($('#fecha_hasta_timbrado').val()) == '') ) {
                errores += ' <li>Complete los datos Requeridos</li> ';
            }
		}

        if(vacio(errores)){ //si no hay errores enviamos
            console.log('ok, Enviando');
            $('#'+formuId).submit();
            return true;
        }else{ //mostramos los errores
            var html = '';
                html += '<strong>Error!</strong> Por favor revise los datos que ha ingresado.<br><br>';
                html += "<ul>"+errores+"</ul>";

            $('#errores').html(html).promise().done(function(){
                $(this).slideToggle('slow');
            });
            return false;
        }
    }


	$('input').focus(function(){ $(this).select(); });



function limpiarCadena(cadena){
   // Definimos los caracteres que queremos eliminar
   var specialChars = "!@#$^&%*()+=-[]\/{}|:<>?,.";

   // Los eliminamos todos
   for (var i = 0; i < specialChars.length; i++) {
       cadena= cadena.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
   }

   // Lo queremos devolver limpio en minusculas
   cadena = cadena.toLowerCase();

   // Quitamos espacios y los sustituimos por _ porque nos gusta mas asi
   cadena = cadena.replace(/ /g,"_");

   // Quitamos acentos y "ñ". Fijate en que va sin comillas el primer parametro
   cadena = cadena.replace(/á/gi,"a");
   cadena = cadena.replace(/é/gi,"e");
   cadena = cadena.replace(/í/gi,"i");
   cadena = cadena.replace(/ó/gi,"o");
   cadena = cadena.replace(/ú/gi,"u");
   cadena = cadena.replace(/ñ/gi,"n");
   return cadena;
}


	function redondear(numero){
		var flotante = parseFloat(numero);
		var resultado = Math.round(flotante);
		return resultado;
	}

    function trim(cadena){
	for(var i = 0; i < cadena.length; ){
		if(cadena.charAt(i) == ' '){
			cadena = cadena.substring(i+1, cadena.length);
		}else{
			break;
		}
	}
	for(var i = cadena.length - 1; i >= 0; i=cadena.length-1){
		if(cadena.charAt(i) == ' '){
			cadena = cadena.substring(0, i);
		}else{
			break;
		}
	}
	return cadena;
}

function vacio(q) {  //recordar pasar el valor no el campo!
	for ( i = 0; i < q.length; i++ ) {
    	if ( q.charAt(i) != " " ) {
        	return false;
        }
    }
	return true;
}

//Numeros a letras copiado y adaptado de: https://gist.github.com/alfchee/e563340276f89b22042a
function Unidades(num){
	switch(num)
	{
		case 1: return "UN";
		case 2: return "DOS";
		case 3: return "TRES";
		case 4: return "CUATRO";
		case 5: return "CINCO";
		case 6: return "SEIS";
		case 7: return "SIETE";
		case 8: return "OCHO";
		case 9: return "NUEVE";
	}

	return "";
}//Unidades()

function Decenas(num){

	var decena = Math.floor(num/10);
	var unidad = num - (decena * 10);

	switch(decena)
	{
		case 1:
			switch(unidad)
			{
				case 0: return "diez";
				case 1: return "once";
				case 2: return "doce";
				case 3: return "trece";
				case 4: return "catorce";
				case 5: return "quince";
				default: return "dieci" + Unidades(unidad);
			}
		case 2:
			switch(unidad)
			{
				case 0: return "veinte";
				default: return "veinti" + Unidades(unidad);
			}
		case 3: return DecenasY("treinta", unidad);
		case 4: return DecenasY("cuarenta", unidad);
		case 5: return DecenasY("cincuenta", unidad);
		case 6: return DecenasY("sesenta", unidad);
		case 7: return DecenasY("setenta", unidad);
		case 8: return DecenasY("ochenta", unidad);
		case 9: return DecenasY("noventa", unidad);
		case 0: return Unidades(unidad);
	}
}//Unidades()

function DecenasY(strSin, numUnidades) {
if (numUnidades > 0)
return strSin + " y " + Unidades(numUnidades)

return strSin;
}//DecenasY()

function Centenas(num) {
	var centenas = Math.floor(num / 100);
	var decenas = num - (centenas * 100);

	switch(centenas)
	{
		case 1:
			if (decenas > 0)
				return "ciento " + Decenas(decenas);
			return "cien";
		case 2: return "doscientos " + Decenas(decenas);
		case 3: return "trescientos " + Decenas(decenas);
		case 4: return "cuatrocientos " + Decenas(decenas);
		case 5: return "quinientos " + Decenas(decenas);
		case 6: return "seiscientos " + Decenas(decenas);
		case 7: return "setecientos " + Decenas(decenas);
		case 8: return "ochocientos " + Decenas(decenas);
		case 9: return "novecientos " + Decenas(decenas);
	}

	return Decenas(decenas);
}//Centenas()

function Seccion(num, divisor, strSingular, strPlural) {
	var cientos = Math.floor(num / divisor)
	var resto = num - (cientos * divisor)

	var letras = "";

	if (cientos > 0)
		if (cientos > 1)
			letras = Centenas(cientos) + " " + strPlural;
		else
			letras = strSingular;

	if (resto > 0)
		letras += "";

	return letras;
}//Seccion()

function Miles(num) {
	var divisor = 1000;
	var cientos = Math.floor(num / divisor)
	var resto = num - (cientos * divisor)

	var strMiles = Seccion(num, divisor, "un mil", "mil");
	var strCentenas = Centenas(resto);

	if(strMiles == "")
		return strCentenas;

	return strMiles + " " + strCentenas;
}//Miles()

function Millones(num) {
	var divisor = 1000000;
	var cientos = Math.floor(num / divisor)
	var resto = num - (cientos * divisor)

	var strMillones = Seccion(num, divisor, "un millon de", "millones de");
	var strMiles = Miles(resto);

	if(strMillones == "")
		return strMiles;

	return strMillones + " " + strMiles;
}//Millones()

function NumeroALetras(num){
	console.log('num=>'+num);
	var data = {
		numero: num,
		enteros: Math.floor(num),
		centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
		letrasCentavos: "",
		letrasMonedaPlural: 'Guaranies ',//"PESOS", 'Dólares', 'Bolívares', 'etcs'
		letrasMonedaSingular: 'Guarani ', //"PESO", 'Dólar', 'Bolivar', 'etc'
		letrasMonedaCentavoPlural: '',
		letrasMonedaCentavoSingular: ''
	};

	if (data.centavos > 0) {
		data.letrasCentavos = "CON " + (function (){
			if (data.centavos == 1)
				return Millones(data.centavos) + " " + data.letrasMonedaCentavoSingular;
			else
				return Millones(data.centavos) + " " + data.letrasMonedaCentavoPlural;
			})();
	};

	if(data.enteros == 0)
		return data.letrasMonedaPlural +" CERO " + " " + data.letrasCentavos;
	if (data.enteros == 1)
		return data.letrasMonedaPlural + Millones(data.enteros) +  " " + data.letrasCentavos;
	else
		return data.letrasMonedaPlural + Millones(data.enteros) + " " +  data.letrasCentavos;
}//NumeroALetras()

//ejemplo claro de JS+POO
//https://www.yoelprogramador.com/formatear-numeros-con-javascript/
var puntitos = {
	separador: ".", // separador para los miles
	sepDecimal: ',', // separador para los decimales

	formatear:function (nume){
		//nume = redondear(nume); //no se puede usar una funcion que no este en la clase
		nume +='';
		var splitStr = nume.split('.');
		var splitLeft = splitStr[0];
		var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
		var regx = /(\d+)(\d{3})/;
		while (regx.test(splitLeft)) {
			splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
		}
		return  splitLeft +splitRight;
	}

}



$('#crudModal').on('show.bs.modal', function (event) {
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

    $("#modal-btn-si").on("click", function(){
        document.getElementById(form).submit();
    });

});





</script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#btnPrintArea").click(function () {
            $("div#myPrintArea").printArea();
        })
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
	$('#crudModal').on('show.bs.modal', function (event) {
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

    $("#modal-btn-si").on("click", function(){
        document.getElementById(form).submit();
    });

});
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
	function direccion(url){
		alert(url);
		alert("alan");
	}
});

</script>
