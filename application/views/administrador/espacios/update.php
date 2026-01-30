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
            <h2 class="mc-page-header">Espacio</h2>
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
                    <div class="panel-heading">Datos del Espacio</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12  col-md-6">
                                <div class="form-group">
                                    <label for="nombre_espacio_id"><?=ucfirst('nombre espacio');?></label>
                                    <input type="text" class="form-control" id="nombre_espacio_id" 
                                    required name="nombre_espacio" value="<?=$values->nombre_espacio;?>" >
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="init_hora">Hora de inicio</label>
                                    <input type="time" class="form-control" name="init_hora" value="<?=$values->init_hora;?>" placeholder="hora de inicio">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="init_hora">Hora de cierre</label>
                                    <input type="time" class="form-control" name="fin_hora"  value="<?=$values->fin_hora;?>" placeholder="hora de cierre">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="email_notifica">
                                        <p>Notificación</p> 
                                        <small>Se notifica al responsable de area </small>
                                    </label>
                                    <input type="email" class="form-control" name="email_notifica" placeholder="Notificación De Encargado de Espacio" value="<?=$values->email_notifica?>" />
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
                                                value="<?=$values->asoc_from?>">
                                            </div>
                                        </div>                 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="asoc_to">
                                                    <small>Días deshabilitado Despues de la reserva</small>
                                                </label>
                                                <input type="number" class="form-control" id="asoc_to" value="<?=$values->asoc_to?>" name="asoc_to">
                                            </div>
                                        </div>
                                    </div>                          
                                </div>
                            </div>

                            <div class="col-xs-6 col-md-12">
                                <div class="form-group">
                                    <label for="edificio_id_id"><?=ucfirst('Comentario');?></label>
                                    <textarea class="form-control hint2basic"  name="descripcion" rows="10" required ><?=$values->descripcion;?></textarea>
                                </div>
                            </div>  

                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="init_hora">Precisa de Autorización</label>
                                    <select name="autorizacion" class="form-control">
                                        <option  value="0" <?=($values->autorizacion == 0)? "selected":""?>>No</option>
                                        <option value="1" <?=($values->autorizacion == 1)? "selected":""?>>Si</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="edificio_id_id pull-right">
                                        <?=ucfirst('Foto del espacio');?>
                                        <?php if(!empty($values->foto_espacio)){ ?>
                                            <a href="<?=base_url('/upload/espacios/'.$values->foto_espacio)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                                        <?php } ?>
                                    </label>
                                    <input type="file" class="form-control" name="foto_espacio">
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="edificio_id_id">
                                        <?=ucfirst('reglamento');?>
                                        <?php if(!empty($values->reglamento)){ ?>
                                            <a href="<?=base_url('/upload/espacios/'.$values->reglamento)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                                        <?php } ?>
                                    </label>
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
                                            <p>Reservas permitidas por unidad</p>
                                            <input type="num" class="form-control" id="periodo_id" placeholder="Cantidad de reservas permitidas" name="max" value="<?=$values->max;?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="max_meses"><strong><?=ucfirst('días');?></strong></label>
                                            <p>Días de anticipación</p>
                                            <input type="num" class="form-control" id="max_meses" placeholder="Días de atincipación" name="max_meses" value="<?=$values->max_meses;?>">
                                        </div>
                                    </div>                  
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cancel_dia"><strong><?=ucfirst('Cancelación');?></strong></label>
                                            <p>Días Para cancelar la reserva</p>
                                            <input type="num" class="form-control" id="cancel_dia" placeholder="Días Para cancelar una reserva" name="cancel_dia" value="<?=$values->cancel_dia;?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cancel_dia">
                                                <strong><?=ucfirst('Restricciones');?></strong>
                                            </label>
                                            <p>Cantidad permitida por persona. Ej: 1, 2, 3...
                                                (Para grupo familiar agregar 0 "cero")</p>
                                            <input type="num" class="form-control" id="cancel_dia"  placeholder="Canidad permitida por persona" name="max_invitados" value="<?=$values->max_invitados;?>">
                                        </div>
                                    </div>
                                    <!--
                                    Pedido del Chateau 
                                    -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cancel_dia">
                                                <strong><?=ucfirst('Tiempo de duracion');?></strong>
                                            </label>
                                     
                                            <p>(Periodo en que los datos anteriormente colocados perduran.)</p>

                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea0"  name="periodo_permitido" value="1" <?=($values->periodo_permitido == 1 )? "checked=\"checked\"":" " ?> />Reservas Activas
                                            </label>                                            

                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea1"   name="periodo_permitido" value="2" <?=($values->periodo_permitido == 2 )? "checked=\"checked\"":" " ?> />Anual
                                            </label>
                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea2"   name="periodo_permitido" Value="3" <?=($values->periodo_permitido == 3 )? "checked=\"checked\"":" " ?> />Mensual
                                            </label>
                                            <label class="checkbox-inline">
                                              <input type="radio" id="checkboxEnLinea3"   name="periodo_permitido" Value="4" <?=($values->periodo_permitido == 4 )? "checked=\"checked\"":" " ?> />Semanal
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
                                                <option value="1" <?=( 1 == $values->tiempo_espera)? "selected":" " ?>>Al instante</option>                                
                                                <option value="0" <?=( 0 == $values->tiempo_espera)? "selected":" " ?>>Día siguiente</option>                                
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="col-md-3"> 
                                        <div class="form-group">
                                            <label for="periodo_id">
                                                <strong><?=ucfirst('Tiempo de espera para realizar una reserva');?></strong>
                                            </label>
                                            <p>(Si el espacio no puede ser reservado el mismo dia, por su preparación, esta opción le brinda la oportunidad de generar ese tiempo en diás)</p>
                                            <input type="num" class="form-control" id="periodo_id"  placeholder="Diás inhabilitados para una reserva" name="bloqueado" value="<?=$values->bloqueado?>">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <?php 
                        $dias = json_decode($values->dias);
                        ?>

                        <div class="col-xs-12 col-md-4">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <p><strong><?=ucfirst('Espacio Asociado');?></strong><br/>
                                        Este espacio se cerrara el mismo dia de la reserva</p>
                                        <select name="asoc_espacio_id"  class="form-control" id="asoc_espacio_id" > 
                                            <option>Seleccione</option>
                                            <?php foreach ($espacios->result()  as $value) { ?>
                                                <option value="<?=$value->id?>" 
                                                    <?=($value->id == $values->asoc_espacio_id)? "selected=\"selected\"":""?> >
                                                    <?=$value->nombre_espacio?> 
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>    
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-4">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="weekDays-selector">
                                        <div class="form-group">
                                            <p><strong><?=ucfirst('Selecione días');?></strong><br/>
                                           Seleccione los días que estará el espacio abierto</p>
                                        </div>
                                        <input type="checkbox" id="weekday-mon" class="weekday" name="day[]" value="1" <?=(@in_array(1, $dias))? "checked":" "?> />
                                        <label for="weekday-mon">Lun</label>
                                        <input type="checkbox" id="weekday-tue" class="weekday" name="day[]" value="2" <?=(@in_array(2, $dias))? "checked":" "?>/>
                                        <label for="weekday-tue">Mar</label>
                                        <input type="checkbox" id="weekday-wed" class="weekday" name="day[]" value="3"  <?=(@in_array(3, $dias))? "checked":" "?>/>
                                        <label for="weekday-wed">Mier</label>
                                        <input type="checkbox" id="weekday-thu" class="weekday" name="day[]" value="4" <?=(@in_array(4, $dias))? "checked":" "?>/>
                                        <label for="weekday-thu">Jue</label>
                                        <input type="checkbox" id="weekday-fri" class="weekday" name="day[]" value="5" <?=(@in_array(5, $dias))? "checked":" "?>/>
                                        <label for="weekday-fri">Vier</label>
                                        <input type="checkbox" id="weekday-sat" class="weekday" name="day[]" value="6" <?=(@in_array(6, $dias))? "checked":" "?>/>
                                        <label for="weekday-sat">Sab</label>
                                        <input type="checkbox" id="weekday-sun" class="weekday" name="day[]" value="0" <?=(@in_array(0, $dias))? "checked":" "?> />
                                        <label for="weekday-sun">Dom</label>                    
                                        <input type="checkbox" id="select_all" class="weekday" name="day[]" <?=(count($dias) > 6)? "checked": ""?> />
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
                                        <option value="1"  <?=(1 == $values->permiso_reserva)? "selected":" " ?> >Si</option>                                
                                        <option value="0"  <?=( 0 == $values->permiso_reserva)? "selected":" " ?> >No</option>                                
                                    </select>
                                </div>
                            </div>       
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="view_reservation">
                                        <?=ucfirst('Las reservas pueden ser vistas por los propietarios?');?>
                                    </label>
                                    <select class="form-control" id="permiso_reserva" 
                                        name="view_reservation" required="required">
                                        <option value="1"  <?=(1 == $values->view_reservation)? "selected":" " ?> >Si</option>      
                                        <option value="0"  <?=( 0 == $values->view_reservation)? "selected":" " ?> >No</option>                                
                                    </select>
                                </div>
                            </div>                 
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="ilim_permiso_reserva"><?=ucfirst('Los administradores puede reservar de forma ilimitada');?></label>
                                    <select class="form-control" id="ilim_permiso_reserva" 
                                    name="ilim_permiso_reserva" required="required">
                                        <option value="1" <?=( 1 == $values->ilim_permiso_reserva)? "selected":" " ?>>Si</option>                                
                                        <option value="0" <?=( 0 == $values->ilim_permiso_reserva)? "selected":" " ?>>No</option>                                
                                    </select>
                                </div>
                            </div>                              
                   
                        </div>
                    </div>
                </div>

                <div class="panel panel-default" >
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
                                    </div>
                                    <?php foreach ($turnos->result() as $turno){ ?>
                                        <div class="row panales_hora">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="init_hora">Identificación</label>
                                                    <input type="text" class="form-control" name="identificacion[]" value="<?=$turno->identificacion?>" required="required"  readonly="readonly">
                                                    <input type="hidden" name="turno_id[]" value="<?=$turno->id?>">
                                                </div>
                                            </div>                      
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="init_hora">Horas</label>
                                                    <input type="time" class="form-control" name="turno[]" value="<?=$turno->turno?>" required="required"  readonly="readonly" >
                                                </div>
                                            </div>                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="init_hora">Importe</label>
                                                    <input type="number" class="form-control" id="turnos" name="importe[]" value="<?=$turno->importe?>" required="required" >
                                                </div>
                                            </div>
                                        </div>
                                    <?php  } ?>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="periodo">
                                    <div class="row panales_periodo">
                                    <p>
                                        PERIODO es el lapso de tiempo en el cual puede reservar un espacio. Ej: A la tarde (en un determinado horario) o a la noche (en otro determinado horario)<br>
                                        Complete los periodos del espacio<br>
                                        Horas (periodo de tiempo - siempre agregar AM/PM si la configuración de su ordenador lo requiere)
                                    </p>
                                    </div>
                                    <?php foreach ($periodos->result() as $periodo) { ?>
                                        <div class="row panales_periodo">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="desde">Desde</label>
                                                    <input type="time" class="form-control" name="desde[]" value="<?=$periodo->desde?>" readonly="readonly">
                                                    <input type="hidden" name="periodo_id[]" value="<?=$periodo->id?>">
                                                </div>
                                            </div> 

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="hasta">Hasta</label>
                                                    <input type="time" class="form-control" name="hasta[]" value="<?=$periodo->hasta?>" readonly="readonly">
                                                </div>
                                            </div> 

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="hasta">Importe</label>
                                                    <input type="number" class="form-control" name="importe_periodo[]" value="<?=$periodo->importe?>" readonly="readonly" >
                                                </div>
                                            </div>        
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name_periodo">Nombre</label>
                                                    <input type="number" class="form-control" name="name_periodo[]" value="<?=$periodo->name_cant?>" placeholder="personas" readonly="readonly">
                                                </div>
                                            </div>        
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="cant_periodo">Cantidad U.F</label>
                                                    <input type="number" class="form-control" name="cant_periodo[]" value="<?=$periodo->cantidad?>" placeholder="cantidad" readonly="readonly">
                                                </div>
                                            </div>                                          
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">   
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="active"><?=ucfirst('Estado del espacio,');?> <br><small>(Usted puede activar y desactivar el espacio para que deje de ser visualizado por los propietarios)</small></label>
                            <select class="form-control" id="active" 
                            name="active" required="required">
                                <option value="1" <?=( 1 == $values->active)? "selected":" " ?>>Espacio Activo</option>                                
                                <option value="0" <?=( 0 == $values->active)? "selected":" " ?>>Espacio Desactivado</option>                                
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>    
                <hr>   
                <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var horas;
    var periodo;

    $(document).ready(function (){

        var horas = $('.panales_hora').length;          
        var periodo = $('.panales_periodo').length;

        $("#espacios_form").submit(function(e){
            
            var flag = 0;
            var num_horas = $('.panales_hora').length;          
            var num_periodo = $('.panales_periodo').length;
            

            if(num_horas > 1 ){
                $("#espacios_form").submit();
            }else{
                flag = 1;
            }             

            if(num_periodo > 1 ){
                $("#espacios_form").submit();
            }else{ 
                flag = 1;
            }       

   
            if(flag == 1){
                e.preventDefault();
                message = "Compruebe los horarios o periodo del espacio";
                Swal.fire(
                    message,
                    '',
                    'info'
                )
            }
            
        });


        $('#select_all').change(function() {
            var checkboxes = $(this).closest('form').find(':checkbox');
            if($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
        });

    })

</script>