$(document).ready(function() {


	$('#edificios_form').keypress(function(e){
		if(e.which == 13){
			return false;
		}
	})
	//al pulsar en un campo del calendario
	
	$('.turnos').on('click',function(){
		$('.turnos').removeClass('active');
		$(this).addClass('active');
		var turno_id = $(this).data('id');
		$("#turno_id").val(turno_id); 
	});

	$('.dia').on('click', function() { 
		//obtenemos el número del día
		var num_dia = $(this).find('div').html();

		//reservas nos da el número de reservas para ese día 
		//ejemplo: 1 = solo 1 hora escogida, sobran 10 horas ese día
		var reservas = $(this).find('div').attr('id');
		//console.log(reservas)

		//obtenemos el día de hoy
		var hoy = $(this).find('.highlight').html();

		//si pulsamos en un cuadro que no es un día mostramos el siguiente mensaje
		if ($(this).find('div').hasClass('fest')) 
		{
			new_popup("Por favor seleccionar día disponible.","Error");
			return false;
		}	
			//si pulsamos en un cuadro que no es un día mostramos el siguiente mensaje
		if ($(this).find('div').hasClass('rech')) 
		{
			new_popup("Este día fue cerrado por el administrador","Error");
			return false;
		}
		//si pulsamos en un cuadro que no es un día mostramos el siguiente mensaje
		if (num_dia == null) 
		{
			new_popup("Por favor seleccionar día disponible..","Error");
			return false;
		}
		//si es hoy podemos decimos que no se puede reservar para hoy
		/*if (hoy) 
		{
			new_popup("Hoy no se pueden hacer reservas.","Hoy");
			return false;
		}*/

		//obtenemos el año a través del campo oculto del formulario
		var year = $(".year").val();

		//obtenemos el mes a través del campo oculto del formulario
		var month = $(".month").val();

		//obtenemos el mes a través del campo oculto del formulario
		//y le restamos uno porque en javascript los meses igual que
		//los días empiezan en 0, si es enero debe ser 0 y no 1
		var monthjs = $(".month").val() - 1;

		//anteponemos el 0 si es un sólo número para poder trabajar
		//correctamente la fecha
		if (num_dia.lenght == 1) {
			num_dia = '0' + num_dia;
		}

		//anteponemos el 0 si es un sólo número para poder trabajar
		//correctamente la fecha
		if (month.lenght == 1) {
			month = '0' + month;
		}

		//creamos la fecha sobre la que el usuario ha pulsado
		var fecha = new Date(year, monthjs, num_dia);

		//creamos un array con los meses de año para mostrar un mensaje final
		//más útil para el usuario
		var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		var mes_escogido = meses[fecha.getMonth()];

		//lo mismo que con el mes, pero con los días de la semana
		var dias_semana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
		var dia_escogido = dias_semana[fecha.getDay()];

		/*
		//si es sábado o domingo, 0 es domingo y 6 es sábado en javascript
		if ((fecha.getDay() == 0) || (fecha.getDay() == 6)) 
		{

			new_popup("Sábados y Domingos festivos, escoge un dia laboral.", "Dia festivo");
			return false;

		}
*/
		//si es un día anterior al día de hoy pero si se puede cargar hoy
		fecha_actual =  new Date();
		if (fecha < fecha_actual && num_dia != hoy ) 
		{
			new_popup("Escoja un dia actual.","Dia anterior");
			return false;
		}

		//si es distinto de nulo significa que hemos pulsado en un cuadro
		//del calendario que tiene número
		//si num no es null, significa que el usuario ha picado en un campo del formulario que contiene números dejamos seguir
		if (num_dia != null) {
			//si el día ya contiene el límite de visitas no permitimos insertar nuevos registros
			//de 10 a 20 incluídas van 11 horas, así que es oc_11
			if (reservas == 'oc_11') 
			{
				new_popup("El " + dia_escogido + " " + num_dia + " de " + mes_escogido + " de " + year + " esta completo.","Dia completo");
				//en otro caso si
			} else {
				year = $('.year').val();
				month = $(".month").val();
				espacio_id = $(".espacio_id").val();
				turno_id = $(".turno_id").val();
				//console.log(num_dia + "-" + month + "-" + year)
				$.ajax({
					type : 'POST',
					url : base_url+controlador+'/coger_hora',
					data : {
						'num' : num_dia,
						'year' : year,
						'month' : month,
						'espacio_id': espacio_id,
						'turno_id': turno_id,
						'dia_escogido' : dia_escogido,
						'mes_escogido' : mes_escogido
					},
					beforeSend : function() {

					},
					success : function(data) {
						if (data) {
				

						/*	var response=jQuery.parseJSON(data);

							if(typeof response =='object')
							{
	 							
							}*/

							try { 
								json = $.parseJSON(data);
								if(json = $.parseJSON(data)){
									new_popup(json.error, "Error formulario");
									return false;
								}
							 } 
							catch(e) { 
							 	$('#midiv').html(data);
								$("#midiv").dialog({
									modal : true,
									width : 480,
									title : 'Nueva reserva',
									buttons : {
										"Confirmar reserva" : function() 
										{
											

											//validamos el formulario con 
											if(!$('#form_coger_reserva')[0].checkValidity()){
												console.log($('#form_coger_reserva')[0].checkValidity());
												new_popup("Verifique que los campos esten esten completos correctamente", "Error formulario");
												return;
											}
											if($("#desde_hora").val() == ""){
												new_popup("Debes seleccionar  una hora.", "Error formulario");
												return;
											}
											
											if($("#unidad_id").val() == ""){
												new_popup("Debes seleccionar  una Unidad.", "Error formulario");
												return;
											}
											if(controlador == 'administrador'){
												question();
											}else{
												send_form();
											}
											return false;
										},
										"Cerrar" : function() {
											$(this).dialog('close')
											.addClass("btn btn-default btn-sm");
										}
									}
								});
							} 
							
						}
					},
					error : function() {
						alert('Ha habido un error, inténtalo de nuevo más tarde');
					}
				});
				return false;
			}

		}

	})
}); 

//mostramos un popup con un error personalizado pasando el mensaje y el título 
//que queremos mostrar, al ser una cosa demasiado repetitiva mejor escribirla una
//vez y llamarla las veces necesarias
var send_form = function(){
	$.ajax({	
		url : $('#form_coger_reserva').attr("action"),
		type : 'POST',
		data : $('#form_coger_reserva').serialize(),
		beforeSend : function() {
			$("#midiv").dialog('close');
			$('<div class="procesando">Procesando la petición, espere por favor</div>').dialog({
				modal : true,
				title : 'Procesando petición',
			});
			
		},
		success : function(data) {
			$(".procesando").dialog('close');
			new_popup( data , "reserva concertada");
			$(".procesando").dialog('close');
			load();
		},
		error : function() {
			$('.formulario_reservas').html("<div>Ha ocurrido un error, intentelo de nuevo más tarde.</div>");
		}
	});
}

var question = function(){
	var data = $('#form_coger_reserva').serialize();
	var url = base_url+controlador+'/check_persmisos';
	$.post(url,data,function(text){
		
		if(text == 1){
			send_form();
		}else{
			Swal.fire({
			  title: 'Quiere Continuar?',
			  text: text,
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'SI',
			  cancelButtonText: 'No',
			}).then((result) => {
				if (result.value) {
					send_form();
				}else{
					return false;
				}
			})
		}
		
	})
}


function new_popup(message, titulo)
{
	Swal.fire(
		message,
		'',
		'info'
	)
}