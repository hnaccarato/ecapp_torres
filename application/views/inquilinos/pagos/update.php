<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-7">
      <h2 class="mc-page-header">Editar Avisos</h2>
    </div>
    <div class="col-xs-12 col-sm-5">
      <ul class="mc-page-actions">
        <li>
          <a type="button" href="<?=base_url();?>admin/cartelera_list"><i class="fa fa-arrow-circle-o-left"></i></a>
        </li>
      </ul>
    </div>
    <div class="col-xs-12">
      <form method="POST" name="cartelera_form" id="cartelera_form">
        <div class="row">

          <div class="col-xs-6">
              <div class="form-group">
                  <label for="edificio_id_id"><?=ucfirst('edificio');?></label>
                  <select class="form-control" name="edificio_id" >
                          <option value="">Seleccione</option> 
                      <?php foreach ($edificios->result() as $edificio) {?>
                          <option value="<?=$edificio->id?>" <?=($edificio->id == $values->edificio_id)? "selected" : "" ?> ><?=$edificio->nombre?></option> 
                      <?php } ?>
                  </select>
              </div>
          </div>  
          <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                  <label for="titulo_id"><?=ucfirst('titulo');?></label>
                  <input type="text" class="form-control" id="titulo_id" placeholder="" name="titulo" value="<?=$values->titulo?>">
              </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label for="fecha_id"><?=ucfirst('fecha');?></label>
              <input type="text" class="form-control datepicker" id="fecha_id" placeholder="fecha" name="fecha" value="<?=$values->fecha;?>">
            </div>
          </div>          

          <div class="col-xs-6">
            <div class="form-group">
              <label for="fecha_envio_id"><?=ucfirst('fecha del envio');?></label>
              <input type="text" class="form-control datepicker" id="fecha_envio_id" placeholder="fecha_envio_id" name="fecha_envio" value="<?=$values->fecha_envio;?>">
            </div>
          </div>

          <div class="col-xs-12 col-sm-12">
            <div class="form-group">
              <label for="detalle_id"><?=ucfirst('detalle');?></label>
              <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"><?=$values->detalle;?></textarea>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <label for="autorizacion_id"><?=ucfirst('Enviados');?></label>
              <select class="form-control" id="autorizacion_id" name="autorizacion" required>
                  <option>Seleccione</option> 
                  <option value="Si" <?=($values->autorizacion == 'Si')? "selected":" " ?>>Si</option>
                  <option value="No"  <?=($values->autorizacion == 'No')? "selected":" " ?>>No</option>
              </select>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-mk-primary pull-right">Submit</button>

      </form>

    </div>
  </div>
</div>  

<script type="text/javascript">
$(document).ready(function (){
  $('.datepicker').datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayHighlight: true
  });
})
</script>