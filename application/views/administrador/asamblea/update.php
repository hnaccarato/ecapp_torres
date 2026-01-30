<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Editar Asamblea</h2>
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
            <form method="POST" name="cartelera_form" id="cartelera_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">


                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('fecha');?></label>
                            <input type="text" class="form-control datepicker" id="fecha_id" placeholder="fecha" name="fecha_envio" value="<?=$values->fecha_envio;?>">
                        </div>
                    </div>          

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="" name="titulo" value="<?=$values->titulo?>">
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
                        <?php if(!empty($values->file)){ ?>

                            <p class="col-sm-4"><?=ucfirst("Convocatoria y Poder")?> 
                            <a href="<?=base_url('/upload/asamblea/'.$values->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                        </p>

                    <?php } ?>            

                    <?php if(!empty($values->memoria_balanse)){ ?>

                        <p class="col-sm-4"><?=ucfirst("Memoria y Balance")?> 
                        <a href="<?=base_url('/upload/asamblea/'.$values->memoria_balanse)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                    </p>

                <?php } ?>            

                <?php if(!empty($values->acta_asamblea)){ ?>

                    <p class="col-sm-4"><?=ucfirst("Acta de Asamblea")?> 
                    <a href="<?=base_url('/upload/asamblea/'.$values->acta_asamblea)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>

            <?php } ?>                
            <?php if(!empty($values->acta_other)){ ?>

                    <p class="col-sm-4"><?=ucfirst("Presentación / Otros'")?> 
                    <a href="<?=base_url('/upload/asamblea/'.$values->acta_other)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>

            <?php } ?>

        </div>
        <div class="col-xs-12 col-sm-12">
            <div class="form-group">
                <label for="detalle_id"><?=ucfirst('detalle');?></label>
                <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"><?=$values->detalle;?></textarea>
            </div>
        </div>

        <div class="col-xs-12  col-sm-6">
            <div class="form-group">
                <label for="estado_id"><?=ucfirst('Subir a la Pagina');?></label>
                <select class="form-control" id="estado_id" name="estado_id" required>
                    <option value="1" <?=($values->estado_id == 1)? "selected":"" ?> >No</option> 
                    <option value="5" <?=($values->estado_id == 5)? "selected":"" ?> >Si</option>
                </select>
            </div>
        </div>

    </div>
    <button type="submit" class="btn btn-mk-primary pull-left">Enviar</button>

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