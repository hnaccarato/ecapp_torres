
<style type="text/css">
.reservas{
   max-height: 200px !important;
}
</style>

<div class="formulario_reservas">
  <?php $attr = array('name' => 'form_coger_reserva', 'id' => 'form_coger_reserva'); ?>     
  <?=form_open(base_url('inquilinos/nueva_reserva'), $attr) ?>
  <h5 class="text-center">
    <strong>
    reserva para el <?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?>
    </strong>
  </h5>
  <br />
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group">
        <label>Desde</label>
        <select name="hora" id="desde_hora" class="form-control" >
          <option value="">Escoge una hora</option>
          <?php
          foreach($info_dia as $fila)
            { ?>
              <option value=<?=$fila->hora_reserva ?>>
                <?=$fila->hora_reserva ?> -  <?=$fila->hasta ?>
              </option>
              <?php   
            }
            ?>      
          </select>                         
        </div>  
    </div>
  </div>
  <div class="table-responsive reservas">
    <table class="table mc-read-table" id="table-read">
      <thead>
        <tr>
          <td data-column="espacios.fin_hora"><?=ucfirst('día');?></td>  
          <td data-column="espacios.fin_hora"><?=ucfirst('desde');?></td>  
          <td data-column="espacios.fin_hora"><?=ucfirst('hasta');?></td>  
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservas->result() as  $reserva) {?>
        <tr>
          <td><?=$reserva->dia_calendario?></td>
          <td><?=$reserva->hora_reserva?></td>
          <td><?=$reserva->hora_hasta?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>   
  </div>  
  <input type="hidden" name="dia_update" value="<?=$year ?>-<?=$month ?>-<?=$dia ?>" />
  <input type="hidden" name="fecha_escogida" value="<?=ucfirst($dia_escogido) ?> <?=$dia ?> de <?=$mes_escogido ?> de <?=$year ?>" />                 
  <input type="hidden" name="espacio_id" value="<?=$espacio_id ?>" />                 
  <input type="hidden" name="turno_id" value="<?=$turno->id?>" />
  <input type="hidden" name="autorizacion" id="autorizacion" value="<?=$values->autorizacion?>" />                                        
  <?=form_close();?>
</div>
