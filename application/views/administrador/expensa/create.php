<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">subida de archivo - Expensa</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/expensa_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">
    <div class="panel panel-default">
    <div class="panel-body">
    <hr>    
    <div class="row">    
        <div class="col-xs-12">
            <form method="POST" name="edificios_form" id="edificios_form" enctype="multipart/form-data" autocomplete="off" acction="<?=base_url('administrador/expensa_create');?>">

                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 12px;">
                        <p>Usted puede adjuntar el listado completo de expensa. Descargue el modelo ejemplo. Recuerde que si lo sube dos veces las expensa se duplicaran</p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="base"><?=ucfirst('lista');?> 
                                <span class="pull-right" style="margin-left:20px !important;"> <a href="<?=base_url('upload/ejemploExpensa.csv')?>">(Descargar Ejemplo )</a></span>
                            </label>
                            <input type="file" class="form-control" id="base" placeholder="" 
                            name="base_expensa">
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>

            </form>
        </div>
        </div>
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