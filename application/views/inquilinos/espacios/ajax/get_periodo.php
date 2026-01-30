<div class="panel panel-default">
    <div class="panel-body">
        <div class="formulario_reservas">
            <?php $attr = array('name' => 'form_coger_reserva', 'id' => 'form_coger_reserva'); ?>     
            <?=form_open(base_url('inquilinos/reservation'), $attr) ?>
            <h4><?=$values->nombre_espacio?></h4>
            <hr>
            <h5>Reserva para el <?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?></h5><br />
            <div class="col-md-6">
                <div class="form-group">
                    <label>Horario *</label>
                    <select name="hora" id="desde_hora" class="form-control" >
                        <option value="">Escoge una hora *</option>
                        <?php           
                        foreach($info_dia as $fila)
                        {
                            if($fila->estado == 'ocupado')
                            {
                                ?>
                                <option disabled="disabled"  data-id="<?=$fila->id?>"  rel="<?=$fila->periodo_id?>" value="<?=$fila->hora_reserva ?>">
                                    <?=$fila->hora_reserva ?> - <?=$fila->hora_hasta ?>

                                </option> 
                                <?php
                            }else{  
                                ?>
                                <option data-id="<?=$fila->id?>"  rel="<?=$fila->periodo_id?>" value="<?=$fila->hora_reserva?>" >
                                    <?=$fila->hora_reserva ?> - <?=$fila->hora_hasta ?>
                                </option>
                                <?php
                            }
                        }
                        ?>      
                    </select>              
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                   <label>Cantidad</label>
                   <select name="adicionales" id="invitados" class="form-control" >
                        <option value="">Seleccionar Adicionales</option>   
                    </select> 
                </div>
            </div>

      
            <?php if(COVID19): ?>
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div> 
                 <?php if($values->max_invitados > 1){ ?>
                    <div class="col-md-12" id="addinvitados">

                        <div class="form-group">
                           <label>* Total de personas que asistiran a la reserva MAX (<?=$values->max_invitados?>):</label>
                           <input type="number" class="form-control" id="addinvitados"
                           required="required"  name="invitados" min="1" 
                           max="<?=$values->max_invitados?>"  value="1" > 
                        </div>
                    </div>
                 <?php } ?>

                <div class="col-md-12">
                    <hr>
                </div>    
                <div class="col-xs-6">
                    <p style="margin: 9px 0 10px;">
                        Condiciones obligatorias por COVID-19 
                        <a href="<?=base_url('inquilinos/get_tyc');?>"  class="glyphicon glyphicon-save-file" aria-hidden="true"></a>
                    </p>
                   
                </div>
                <div class="col-xs-6">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" required="required" name="acepto" > * Acepto las Condiciones
                        </label>
                    </div>
                </div>
            </div>
        <?php endif ?>
  
            <table class="table mc-read-table" id="table-read">
                <thead>
                    <tr>
                        <td data-column="espacios.fin_hora"><?=ucfirst('día');?></td>  
                        <td data-column="espacios.fin_hora"><?=ucfirst('hora');?></td>  
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas->result() as  $reserva) {?>
                        <tr>
                            <td><?=$reserva->dia_calendario?></td>
                            <td><?=$reserva->hora_reserva?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>                         
            <input type="hidden" name="dia_update" value="<?=$year ?>-<?=$month?>-<?=$dia ?>" />

            <input type="hidden" name="fecha_escogida" value="<?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?>" />                 
            <input type="hidden" name="espacio_id" value="<?=$espacio_id ?>" />                 
            <input type="hidden" name="periodo_id" id="periodo_id" value="" />        
            <input type="hidden" name="reserva_id" id="reserva_id" value="" />              

            <?=form_close();?>
        </div>
    </div>
</div>

<script type="text/javascript">

$("#addinvitados").hide(); 

$("#desde_hora").change(onSelectChange);

    function onSelectChange(){
        var output = "",
        $this = $(this);
        if($this.val() != 0){
            output = $this.find('option:selected').attr('rel');
            reserva_id = $this.find('option:selected').data('id');
            periodo_id = $this.find('option:selected').data('periodo');

            $("#periodo_id").val(output);
            $("#reserva_id").val(reserva_id);
        }

        if($.isNumeric(reserva_id)){
        // alert(hora_reserva);
          $.post(base_url+'inquilinos/check_permitidos/',
            {'reserva_id':reserva_id},
            function(data){
               // alert(data);
               if(data == 0){
                Swal.fire(
                    message,
                    '',
                    'info'
                )
            }else{
               // if(data == 1)
                $("#addinvitados").show();
                load_cant(data);
            }

            }
          );
        }
      
    }

    function load_cant(number){
        $("#invitados").html('');
        for ( var i = 1, l = number; i <= l; i++ ) {


            if(i == 1){
                $("#invitados").append("<option value='"+i+"'>Reserva particular</option>");
            }else{
                $("#invitados").append("<option value='"+i+"'>Reserva adicional "+i+"</option>");  
            }
        }
    } 
</script>