<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Circular</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/circular_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">  
        <div class="col-xs-12">
            <form method="POST" name="circular_form" id="circular_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_envio_id"><?=ucfirst('Fecha cierre');?></label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker"  id="fecha_envio_id" value="<?=date('Y-m-d')?>" placeholder="Fecha del dia ultimo dia de la circular" name="fecha_envio"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                        </div>
                    </div>  
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('Fecha inicio');?></label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker"  id="fecho_id" value="<?=date('Y-m-d')?>"placeholder="fecha del envio" name="fecha"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                        </div>
                    </div>                       
        
                    <div class="col-xs-12 col-xs-6">
                        <div class="form-group">
                            <label for="tipo_servicio_id"><?=ucfirst('Categoria');?></label>
                            <select class="form-control" name="tipo_servicio_id" required>
                                <option>Seleccione</option> 
                                <?php foreach ($categorias->result() as $value) {?>
                                    <option value="<?=$value->id?>"><?=$value->nombre?></option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div>  
                    <div class="col-xs-12 col-md-6 ">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="Titulo del Envio" name="titulo">
                        </div>
                    </div>  
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="file_id"><?=ucfirst('Archivo');?></label>
                            <input type="file" class="form-control" name="file" >
                        </div>
                    </div> 
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="detalle_id"><?=ucfirst('detalle');?></label>
                            <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="autorizado"><?=ucfirst('Enviar a propietarios');?></label>
                            <select class="form-control" id="autorizacion_id" name="estado_id" required>
                                <option value="<?=PENDIENTE?>">No</option>
                                <option value="<?=ENVIADO?>">Si</option>
                            </select>
                        </div>
                    </div>
                </div>
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