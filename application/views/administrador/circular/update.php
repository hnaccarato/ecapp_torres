<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Editar Circular</h2>
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
            <form method="POST" name="cartelera_form" id="cartelera_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="fecha_fin"><?=ucfirst('Fecha cierre');?></label>
                            <input type="text" class="form-control datepicker" id="fecha_fin" placeholder="fecha de cierre" name="fecha_envio" value="<?=$values->fecha_envio;?>">
                        </div>
                    </div> 
                    
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('Fecha inicio');?></label>
                            <input type="text" class="form-control datepicker" id="fecha_id" placeholder="fecha" name="fecha" value="<?=$values->fecha;?>">
                        </div>
                    </div>                      

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="tipo_servicio_id"><?=ucfirst('Categoria');?></label>
                            <select class="form-control" name="tipo_servicio_id" required>
                                <option value="">Seleccione</option> 
                                <?php foreach ($categorias->result() as $tipo) {?>
                                    <?php $selected = '';?>
                                    <?php if($tipo->id == $values->tipo_servicio_id ){
                                        $selected ="selected='selected'";
                                    }
                                    ?>
                                    <option value="<?=$tipo->id?>" <?=$selected?> >
                                        <?=$tipo->nombre?>
                                    </option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div>         

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="" name="titulo" value="<?=$values->titulo?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="file_id"><?=ucfirst('Cargar archivo');?></label>
                            <input type="file" class="form-control" name="file" >
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12">
                        <?php if(!empty($values->file)){ ?>
                            <p class="text-left"><?=ucfirst("Descargar archivo")?> 
                            <a href="<?=base_url('/upload/circular/'.$values->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                        </p>
                    <?php } ?>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <div class="form-group">
                        <label for="detalle_id"><?=ucfirst('detalle');?></label>
                        <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"><?=$values->detalle;?></textarea>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="autorizacion_id">
                            <?=ucfirst('Ver por propietarios');?></label>
                            <select class="form-control" id="autorizacion_id" name="estado_id" 
                            required>
                            <option value="<?=ENVIADO?>" <?=($values->estado_id == ENVIADO)? "selected":" " ?>>Si</option>
                            <option value="<?=PENDIENTE?>"  <?=($values->estado_id == PENDIENTE)? "selected":" " ?>>No</option>
                            <option value="13">Sin Notificar </option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-mk-primary pull-right">Guardar</button>
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