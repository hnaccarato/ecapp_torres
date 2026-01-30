<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-7">
      <h2 class="mc-page-header">Nueva Asamblea</h2>
    </div>
    <div class="col-xs-12 col-sm-5">
      <ul class="mc-page-actions">
        <li>
          <a type="button" href="<?=base_url();?>administrador/asamblea_list"><i class="fa fa-arrow-circle-o-left"></i></a>
        </li>
      </ul>
    </div>
  </div>
  <hr class="hr_subrayado">
  <div class="row">
    <div class="col-xs-12">
      <form method="POST" name="asamblea_form" id="asamblea_form" enctype="multipart/form-data" autocomplete="off">
        <div class="row">  
          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <label for="fecha_envio_id"><?=ucfirst('fecha de la asamblea');?></label>
              <div class="input-group date">
                <input type="text" class="form-control datepicker"  id="fecha_envio_id" value="<?=date('Y-m-d')?>"placeholder="fecha del envio" name="fecha_envio"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
              </div>
            </div>
          </div> 
          <div class="col-xs-12 col-md-6 ">
              <div class="form-group">
                  <label for="titulo_id"><?=ucfirst('titulo');?></label>
                  <input type="text" class="form-control" id="titulo_id" placeholder="Titulo del Envio" name="titulo">
              </div>
          </div>           
          <div class="col-xs-12 col-sm-3">
              <div class="form-group">
                  <label for="file_id"><?=ucfirst('Convocatoria y Poder');?></label>
                  <input type="file" class="form-control" name="file" accept="application/msword,application/pdf,image/*,.zip,.rar">
              </div>
          </div>              
          <div class="col-xs-12 col-sm-3">
              <div class="form-group">
                  <label for="file_id"><?=ucfirst('Memoria y Balance');?></label>
                  <input type="file" class="form-control" name="memoria_balanse" accept="application/msword,application/pdf,image/*,.zip,.rar" >
              </div>
          </div>              
          <div class="col-xs-12 col-sm-3">
              <div class="form-group">
                  <label for="file_id"><?=ucfirst('Acta de Asamblea');?></label>
                  <input type="file" class="form-control" name="acta_asamblea" accept="application/msword,application/pdf,image/*,.zip,.rar">
              </div>
          </div>            
          <div class="col-xs-12 col-sm-3">
              <div class="form-group">
                  <label for="file_id"><?=ucfirst('Presentación / Otros');?></label>
                  <input type="file" class="form-control" name="acta_other" accept="application/msword,application/pdf,image/*,.zip,.rar">
              </div>
          </div>         

          <div class="col-xs-12 col-sm-12">
            <div class="form-group">
              <label for="detalle_id"><?=ucfirst('detalle');?></label>
              <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
        <div class="col-xs-12 col-sm-6 ">
          <div class="form-group">
            <label for="estado_id"><?=ucfirst('Subir a la Pagina');?></label>
            <select class="form-control" id="estado_id" name="estado_id" required>
                <option value="1">No</option> 
                <option value="5">Si</option>
            </select>
          </div>
        </div>
        </div>
        <br/>
        <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i>Guardar</button>

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