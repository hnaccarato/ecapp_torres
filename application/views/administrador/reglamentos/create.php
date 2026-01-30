<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-7">
      <h2 class="mc-page-header">Nuevo Reglamento</h2>
    </div>
    <div class="col-xs-12 col-sm-5">
      <ul class="mc-page-actions">
        <li>
          <a type="button" href="<?=base_url();?>administrador/reglamentos_list"><i class="fa fa-arrow-circle-o-left"></i></a>
        </li>
      </ul>
    </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">  
    <div class="col-xs-12">
      <form method="POST" name="reglamentos_form" id="reglamentos_form" enctype="multipart/form-data" autocomplete="off">
        <div class="row">
          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <label for="name_id"><?=ucfirst('Titulo');?></label>
              <input type="text" class="form-control" id="name_id" placeholder="Titulo de Reglamento" name="titulo">
            </div>
          </div>

          <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                  <label for="file_id"><?=ucfirst('Archivo comprobante');?></label>
                  <input type="file" class="form-control" name="file" >
              </div>
          </div>   
        </div>
        <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>

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