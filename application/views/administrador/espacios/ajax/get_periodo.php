<div class="panel panel-default">
  <div class="panel-body">
   <div class="formulario_reservas">
      <?php $attr = array('name' => 'form_coger_reserva', 'id' => 'form_coger_reserva'); ?>     
          <?=form_open(base_url('administrador/reservation'), $attr) ?>
          <h3><?=$values->nombre_espacio?></h3>
              <h5>reserva para el <?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?></h5><br />
             <div class="row">
               <div class="col-xs-6">
                 <div class="form-group">
                   <label>Unidad *</label>
                   <select name="unidad_id"  id="unidad_id" class="form-control" required="required">
                     <option value="">Seleccione Unidad</option>
                     <?php foreach($unidades->result() as $unidad){?>
                     <option value="<?=$unidad->unidad_id?>"><?=$unidad->unidad_name?> <?=$unidad->departamento?></option>
                     <?php } ?>
                   </select>
                 </div> 
               </div>
               
               <div class="col-xs-6">
                  <div class="form-group">
                   <label>Desde *</label>
                   <select name="hora" id="desde_hora" class="form-control" required="required" >
                       <option value="">Hora</option>
                       <?php           
                       foreach($info_dia as $fila)
                       {?>
                        <option data-id="<?=$fila->id?>" rel="<?=$fila->periodo_id?>" value="<?=$fila->hora_reserva?>" data-periodo="<?=$fila->periodo_id?>" >
                           <?=$fila->hora_reserva ?> -  <?=$fila->hora_hasta ?>
                        </option>
                      <?php }?>      
                   </select>              
                 </div>
               </div>
               <div class="col-xs-12">
                <div class="form-group">
                  <label>Cantidad Adicional</label>
                  <select name="adicionales" id="invitados" class="form-control" >
                       <option value="">Seleccionar Adicionales</option>   
                   </select> 
                 </div>
               </div>
               <?php if($values->max_invitados > 1){ ?>
                 <div class="col-md-12" id="addinvitados">

                     <div class="form-group">
                        <label>* Capacidad maxima (<?=$values->max_invitados?>):</label>
                        <input type="number" class="form-control" id="addinvitados"
                        required="required"  name="invitados" min="1" max="<?=$values->max_invitados?>" > 
                     </div>
                 </div>
               <?php } ?>
             </div>
            <div class="form-group">
              <label>Desactiva el día</label>
              <select name="desactivar_dia" id="disactivar_dia" class="form-control" >
                <option value="0">No</option>
                <option value="1">Si</option>
              </select>                         
            </div>
            <table class="table mc-read-table" id="table-read">
                <thead>
                    <tr>
                      <td data-column="espacios.fin_hora"><?=ucfirst('día');?></td>  
                      <td data-column="espacios.fin_hora"><?=ucfirst('hora');?></td>  
                      <td data-column="espacios.nombre_espacio"><?=ucfirst('unidad');?></td>
                      <td data-column="espacios.init_hora"><?=ucfirst('Nombre');?></td>
                    </tr>
                </thead>
                <tbody>
                  <?php foreach ($reservas->result() as  $reserva) {?>
                    <tr>
                      <td><?=$reserva->dia_calendario?></td>
                      <td><?=$reserva->hora_reserva?></td>
                      <td><?=$reserva->name?><?=$reserva->departamento?></td>
                      <td>
                        <?=(isset($reserva->identificacion))? $reserva->identificacion : $values->nombre_espacio?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
            </table>                      
              <input type="hidden" name="dia_update" value="<?=$year ?>-<?=$month?>-<?=$dia ?>" />
                                          
              <input type="hidden" name="fecha_escogida" value="<?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?>" />                 
              <input type="hidden" name="espacio_id" value="<?=$espacio_id ?>" />                 
              <input type="hidden" name="periodo_id" id="periodo_id" value="" />                 
              <input type="hidden" name="reserva_id" id="reserva_id" value="" />                 
              <input type="hidden" name="autorizacion" id="autorizacion" value="<?=$values->autorizacion?>" />                                                   
             <?=form_close();?>
      </div>
  </div>
</div>
<script type="text/javascript">

  $("#desde_hora").change(onSelectChange);
  $("#addinvitados").hide(); 

  function onSelectChange(){
      var output = "",
      $this = $(this);
      if($this.val() != 0){
          output = $this.find('option:selected').attr('rel');
          reserva_id = $this.find('option:selected').data('id');
          periodo_id = $this.find('option:selected').data('periodo');
          hora_reserva = $this.find('option:selected').val();
          $("#periodo_id").val(output);
          $("#reserva_id").val(reserva_id);
          if($.isNumeric(periodo_id)){
          // alert(hora_reserva);
            $.post(base_url+'administrador/check_permitidos/',
              {'reserva_id':reserva_id},
              function(data){
              //  if(data == 1)
                $("#addinvitados").show();

                load_cant(data);
              }
            );
          }
      }
      
  }

  function load_cant(number){
    $("#invitados").html('');
    for ( var i = 1, l = number; i <= l; i++ ) {
      if (number > 1) {
        $("#invitados").append("<option value='"+i+"'>mesas "+i+"</option>");
      } else {
        $("#invitados").append("<option value='"+i+"'>Cantidad Autorizada "+i+"</option>");
      }
 
    }
  }
</script>