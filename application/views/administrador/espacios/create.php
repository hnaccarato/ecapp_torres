<style type="text/css">
    .weekDays-selector input {
        display: none!important;
    }

    .weekDays-selector input[type=checkbox] + label {
        display: inline-block;
        border-radius: 6px;
        background: #dddddd;
        height: 40px;
        width: 42px;
        margin-right: 3px;
        line-height: 40px;
        text-align: center;
        cursor: pointer;
    }

    .weekDays-selector input[type=checkbox]:checked + label {
        background: #2AD705;
        color: #ffffff;
    }

    #periodo {
        margin: 30px 10px 30px 70px;
    }

    #periodo .row{
        margin-top: 10px;
    }

    #hora{
        padding: 19px 0px 5px 5px;
    }

    .panales_hora .btn {
        margin-top: 24px;
    }

    .panales_periodo .btn {
        margin-top: 24px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Amenities</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/espacios_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">    
        <div class="col-xs-12">
            <form method="POST" name="espacios_form" id="espacios_form" enctype="multipart/form-data" autocomplete="off">


                <div class="panel panel-default">
                    <div class="panel-heading">Datos del Amenity - <strong class="pull-right">(Agregar AM/PM si la configuración de su ordenador lo requiere)</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12  col-md-6">
                                <div class="form-group">
                                    <label for="nombre_espacio_id"><?=ucfirst('nombre espacio');?></label>
                                    <input type="text" class="form-control" id="nombre_espacio_id" placeholder="" required name="nombre_espacio">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="init_hora">Hora de inicio</label>
                                    <input type="time" class="form-control" name="init_hora" required placeholder="hora de inicio"  min="00:00" max="24:00" >
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="init_hora">Hora de cierre</label>
                                    <input type="time" class="form-control" name="fin_hora" required placeholder="hora de cierre" min="00:00" max="24:00">
                                </div>
                            </div>   

                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="init_hora">
                                        <p>Notificación</p> 
                                        <small>Se notifica al responsable de area </small>
                                    </label>
                                    <input type="email" class="form-control" name="email_notifica" placeholder="Notificación De Encargado de Espacio" />
                                </div>
                            </div>


                            <div class="col-md-8">
                                <div class="panel panel-default">

                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <strong>
                                                Para Inhabilitar el espacio para su mantenimiento, complete los siguietes campos
                                            </strong>
                                            <br/>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="asoc_from">
                                                    <small>Días deshabilitado Antes de la reserva</small>
                                                </label>
                                                <input type="number" class="form-control" id="asoc_from"  name="asoc_from" 
                                                value="">
                                            </div>
                                        </div>                 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="asoc_to">
                                                    <small>Días deshabilitado Despues de la reserva</small>
                                                </label>
                                                <input type="number" class="form-control" id="asoc_to" value="" name="asoc_to">
                                            </div>
                                        </div>
                                    </div>                          
                                </div>
                            </div>



                            <div class="col-xs-6 col-md-12">
                                <div class="form-group">
                                    <label for="edificio_id_id"><?=ucfirst('Comentario');?></label>
                                    <textarea class="form-control hint2basic"  name="descripcion" rows="10" required ></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="init_hora">Precisa de Autorización</label>
                                    <select name="autorizacion" class="form-control">
                                        <option  value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="edificio_id_id"><?=ucfirst('Foto del espacio');?></label>
                                    <input type="file" class="form-control" name="foto_espacio">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="edificio_id_id"><?=ucfirst('reglamento');?></label>
                                    <input type="file" class="form-control" name="reglamento">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Condiciones de uso para reservar</div>
                    <div class="panel-body">

                        <div class="col-xs-12 col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-body" id="dys_model">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="periodo_id"><strong><?=ucfirst('Maximo');?></strong></label>
                                            <p>Permitidas por unidad</p>
                                            <input type="num" class="form-control" id="periodo_id"  placeholder="Cantidad de reservas permitidas" name="max">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="max_meses"><strong><?=ucfirst('días');?></strong></label>
                                            <p>Días de anticipación</p>
                                            <input type="num" class="form-control" id="max_meses"  placeholder="Días de anticipación" name="max_meses">
                                        </div>
                                    </div>                  
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cancel_dia"><strong><?=ucfirst('Cancelación');?></strong></label>
                                            <p>Días Para cancelar</p>
                                            <input type="num" class="form-control" id="cancel_dia"  placeholder="Días Para cancelar una reserva" name="cancel_dia">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cancel_dia">
                                                <strong><?=ucfirst('Restricciones');?></strong>
                                            </label>
                                            <p>Cantidad permitida por persona. Ej: 1, 2, 3...
                                                (Para grupo familiar agregar 0 "cero")</p>
                                            <input type="num" class="form-control" id="cancel_dia"  placeholder="Cantidad permitida por persona" name="max_invitados">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cancel_dia">
                                                <strong><?=ucfirst('Tiempo de duracion');?></strong>
                                            </label>
                                     
                                            <p>(Periodo en que los datos anteriormente colocados perduran.)</p>

                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea0" class="form-control" name="periodo_permitido" value="1" checked="checked"> Reservas Activas
                                            </label>                                            

                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea1" class="form-control"  name="periodo_permitido" value="2"> Anual
                                            </label>
                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea2" class="form-control"  name="periodo_permitido" Value="3"> Mensual
                                            </label>
                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea3" class="form-control"  name="periodo_permitido" Value="4"> Semanal
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <label for="tiempo_espera"><?=ucfirst('Continuidad de reserva');?></label>
                                            <p>
                                                (tiempo para volver a reservar)
                                            </p>
                                            <select class="form-control" id="tiempo_espera" 
                                            name="tiempo_espera" required="required">
                                                <option value="1">Al instante</option>                                
                                                <option value="0">Día siguiente</option>                                
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-md-3"> 
                                        <div class="form-group">
                                            <label for="periodo_id">
                                                <strong><?=ucfirst('Tiempo de espacio inactivo');?></strong>
                                            </label>
                                            <p>(Si el amenity no puede ser reservado el mismo dia, por su preparación, esta opción le brinda la oportunidad de generar ese tiempo en diás)</p>
                                            <input type="num" class="form-control" id="periodo_id"  placeholder="Diás inhabilitados para una reserva" name="bloqueado">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xs-12 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <p><strong><?=ucfirst('Espacio Asociado');?></strong><br/>
                                        Este amenity se cerrara el mismo dia de la reserva</p>
                                        <select name="asoc_espacio_id"  class="form-control" id="asoc_espacio_id" > 
                                            <option>Seleccione</option>
                                            <?php foreach ($espacios->result()  as $value) { ?>
                                                <option value="<?=$value->id?>"><?=$value->nombre_espacio?></option>
                                            <?php } ?>
                                        </select>
                                    </div>    
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="weekDays-selector">
                                        <div class="form-group">
                                            <p><strong><?=ucfirst('Selecione días');?></strong><br/>
                                            Seleccione los días que estará el amenity abierto</p>
                                        </div>
                                        <input type="checkbox" id="weekday-mon" class="weekday" name="day[]" value="1" />
                                        <label for="weekday-mon">Lun</label>
                                        <input type="checkbox" id="weekday-tue" class="weekday" name="day[]" value="2" />
                                        <label for="weekday-tue">Mar</label>
                                        <input type="checkbox" id="weekday-wed" class="weekday" name="day[]" value="3" />
                                        <label for="weekday-wed">Mier</label>
                                        <input type="checkbox" id="weekday-thu" class="weekday" name="day[]" value="4" />
                                        <label for="weekday-thu">Jue</label>
                                        <input type="checkbox" id="weekday-fri" class="weekday" name="day[]" value="5" />
                                        <label for="weekday-fri">Vier</label>
                                        <input type="checkbox" id="weekday-sat" class="weekday" name="day[]" value="6" />
                                        <label for="weekday-sat">Sab</label>
                                        <input type="checkbox" id="weekday-sun" class="weekday" name="day[]" value="0" />
                                        <label for="weekday-sun">Dom</label>                    
                                        <input type="checkbox" id="select_all" class="weekday" name="day[]" />
                                        <label for="select_all">Todos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 


                <div class="panel panel-default">
                    <div class="panel-heading">Opciones de reserva</div>
                    <div class="panel-body" id="dys_model">                
                        <div class="row">
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="permiso_reserva"><?=ucfirst('Los administradores pueden generar reservas');?></label>
                                    <select class="form-control" id="permiso_reserva" 
                                    name="permiso_reserva" required="required">
                                        <option value="1" selected="selected">Si</option>
                                        <option value="0">No</option>                                
                                    </select>
                                </div>
                            </div>                                  
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="view_reservation"><?=ucfirst('Las reservas pueden ser vistas por los propietarios?');?></label>
                                    <select class="form-control" id="view_reservation" 
                                    name="view_reservation" required="required">
                                        <option value="0" selected="selected" >No</option>            
                                        <option value="1" >Si</option>                    
                                    </select>
                                </div>
                            </div>                       
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="ilim_permiso_reserva"><?=ucfirst('Puede reservar, de forma ilimitada');?></label>
                                    <select class="form-control" id="ilim_permiso_reserva" 
                                    name="ilim_permiso_reserva" required="required">
                                        <option value="1" >Si</option>                                
                                        <option value="0" selected="selected">No</option>
                                    </select>
                                </div>
                            </div>                    
                        </div>
                    </div>
                </div>          


                <div class="panel panel-default">
                    <div class="panel-heading">Condiciones Horarias</div>
                    <div class="panel-body" id="dys_model">
                        <div >

                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#hora" aria-controls="hora" role="tab" data-toggle="tab">Por Turnos</a>
                                </li>
                                <li role="presentation">
                                    <a href="#periodo" aria-controls="periodo" role="tab" data-toggle="tab">Por periodo</a>
                                </li>
                            </ul>
                            <div class="tab-content">

                                <div role="tabpanel" class="tab-pane active" id="hora">
                                    <div class="row panales_hora after-add-hora">
                                        <div class="col-md-12">
                                            <p>
                                                Usted puede realizar TURNOS fraccionados en minutos u horas. EJ: Fracciona turnos cada 2 horas para su uso.<br>
                                                Complete los turnos del espacio<br>
                                                Identificación (Si es necesario dividir el espacio)<br> 
                                                Horas (Fracción de tiempo - siempre agregar AM/PM si la configuración de su ordenador lo requiere)
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="init_hora">Identificación</label>
                                                <input type="text" class="form-control" name="identificacion[]" 
                                                placeholder="Identificacion">
                                            </div>
                                        </div>                      
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="init_hora">Horas</label>
                                                <input type="time" class="form-control" name="turno[]" placeholder="hora de de los turnos" min="00:00" max="24:00">
                                            </div>
                                        </div>                                            
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="init_hora">Importe</label>
                                                <input type="number" class="form-control" id="turnos" name="importe[]" placeholder="importe del espacio">
                                            </div>
                                        </div>
                                        <div class="col-md-2"> 
                                            <div class="form-group">
                                                <button class="btn btn-success add-hora" type="button">
                                                    <i class="glyphicon glyphicon-plus"></i> Nuevo
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="periodo">
                                    <div class="row panales_periodo after-periodo" >          
                                        <div class="col-md-12">
                                        <p>
                                            PERIODO es el lapso de tiempo en el cual puede reservar un espacio. Ej: A la tarde (en un determinado horario) o a la noche (en otro determinado horario)<br>
                                            Complete los periodos del espacio<br>
                                            Horas (periodo de tiempo - siempre agregar AM/PM si la configuración de su ordenador lo requiere)
                                        </p>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="desde">Desde</label>
                                                <input type="time" class="form-control" name="desde[]" placeholder="Hora Desde" min="00:00" max="24:00">
                                            </div>
                                        </div> 

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="hasta">Hasta</label>
                                                <input type="time" class="form-control" name="hasta[]" placeholder="Hora Hasta" min="00:00" max="24:00">
                                            </div>
                                        </div> 

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="hasta">Importe</label>
                                                <input type="number" class="form-control" name="importe_periodo[]" placeholder="importe">
                                            </div>
                                        </div>   
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name_periodo">Nombre</label>
                                                <input type="text" class="form-control" name="name_periodo[]" placeholder="personas">
                                            </div>
                                        </div>       
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cant_periodo">Cantidad U.F.</label>
                                                <input type="number" class="form-control" name="cant_periodo[]" placeholder="cantidad">
                                            </div>
                                        </div>      
                                        <div class="col-md-2"> 
                                            <div class="form-group">
                                                <button class="btn btn-success add-periodo" type="button">
                                                    <i class="glyphicon glyphicon-plus"></i> Nuevo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>       
                <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar </button>
            </form>
        </div>
    </div>
</div>

<div class="copy hide">
    <div class="row panales_hora">
        <div class="col-md-4">
            <div class="form-group">
                <label for="init_hora">Identificación</label>
                <input type="text" class="form-control" name="identificacion[]" 
                placeholder="Identificacion">
            </div>
        </div>                      
        <div class="col-md-3">
            <div class="form-group">
                <label for="init_hora">Horas</label>
                <input type="time" class="form-control" name="turno[]" placeholder="hora de de los turnos" min="00:00" max="24:00">
            </div>
        </div>                                            
        <div class="col-md-3">
            <div class="form-group">
                <label for="init_hora">Importe</label>
                <input type="number" class="form-control" id="turnos" name="importe[]" placeholder="importe del espacio">
            </div>
        </div>
        <div class="col-md-2"> 
            <div class="form-group">
                <button class="btn btn-danger remove" type="button">
                    <i class="glyphicon glyphicon-remove"></i> Quitar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="copy_periodo hide">
    <div class="row panales_periodo" >

        <div class="col-md-2">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="time" class="form-control" name="desde[]" placeholder="Hora Desde" min="00:00" max="24:00">
            </div>
        </div>         

        <div class="col-md-2">
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="time" class="form-control" name="hasta[]" placeholder="Hora Hasta" min="00:00" max="24:00">
            </div>
        </div> 

        <div class="col-md-2">
            <div class="form-group">
                <label for="importe_periodo">Importe</label>
                <input type="number" class="form-control" name="importe_periodo[]" placeholder="importe">
            </div>
        </div>        
        <div class="col-md-2">
            <div class="form-group">
                <label for="name_periodo">Nombre</label>
                <input type="text" class="form-control" name="name_periodo[]" placeholder="personas">
            </div>
        </div>        
        <div class="col-md-2">
            <div class="form-group">
                <label for="cant_periodo">Cantidad U.F.</label>
                <input type="number" class="form-control" name="cant_periodo[]" placeholder="cantidad">
            </div>
        </div>                                                                             
        <div class="col-md-2"> 
            <div class="form-group">
                <button class="btn btn-danger remove_periodo" type="button">
                    <i class="glyphicon glyphicon-remove"></i> Quitar
                </button>
            </div>
        </div>

    </div>

</div>


<script type="text/javascript">
    $(document).ready(function (){


        $('#select_all').change(function() {
            var checkboxes = $(this).closest('form').find(':checkbox');
            if($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
        });

        $("body").on('click', '.btn_add', function() {
            $("#row_1").clone().appendTo( "#periodo" );;
        })  

        $("body").on('click', '.btn_delete', function() {
            $(this).parent().parent().remove();
        })

        $(".add-hora").click(function(){ 
            var html = $(".copy").html();
            $(".after-add-hora").after(html);
        });

        $("body").on("click",".remove",function(){ 
            $(this).parents(".panales_hora").remove();
        });  

        $(".add-periodo").click(function(){ 
            var html = $(".copy_periodo").html();
            $(".after-periodo").after(html);
        });

        $("body").on("click",".remove_periodo",function(){ 
            $(this).parents(".panales_periodo").remove();
        });  

    })

</script>