<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Enviar Mensaje</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/consultas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">  
        <div class="col-md-12">

            <div class="panel panel-default">
              <div class="panel-body">
                <address>
                    <strong><?=$user->first_name?> <?=$user->last_name?></strong><br>
                    <?=$user->email?><br>
                    <abbr title="Phone">Telefono:</abbr> <?=$user->phone?>
                </address>
              </div>
            </div>
        </div>
        <div class="col-xs-12">
            <form method="POST" name="consultas_form" id="consultas_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">            
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="edificio_id_id"><?=ucfirst('Categoria');?></label>
                            <select class="form-control" name="tipo_consulta_id" required>
                                <option value="">Seleccione</option> 
                                <?php foreach ($tipo_consultas->result() as $tipo) {?>
                                    <option value="<?=$tipo->id?>"><?=$tipo->nombre?></option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div>  
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="file_id"><?=ucfirst('archivo');?></label>
                            <input type="file" class="form-control" name="file" >
                        </div>
                    </div> 
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label for="descripcion_id"><?=ucfirst('titulo');?></label>
                            <input type="detalle" class="form-control" placeholder="titulo" name="detalle">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="detalle_id"><?=ucfirst('descripcion');?></label>
                            <textarea class="form-control hint2basic" rows="6" placeholder="descripcion" name="descripcion"></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-mk-primary"><i class="glyphicon glyphicon-send"></i> Enviar</button>
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