<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Avisos</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>admin/cartelera_list">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">  
        <div class="col-xs-12">
            <form  method="POST" name="cartelera_form" id="cartelera_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_envio_id"><?=ucfirst('fecha');?></label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker"  id="fecha_envio_id" placeholder="fecha del envio" name="fecha_envio" value="<?=date('Y-m-d')?>">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th"></i>
                                </span>
                            </div>
                        </div>
                    </div>   

                    <div class="col-xs-12 col-xs-6">
                        <div class="form-group">
                            <label for="unidad_id"><?=ucfirst('Unidades');?></label>
                            <select class="form-control" name="unidad_id" id="unidad_id" required="required">
                                <option>Seleccione</option> 
                                <?php foreach ($unidades->result() as $unidades) {?>
                                    <option value="<?=$unidades->id?>">
                                        <?=$unidades->name?> - <?=$unidades->departamento?>
                                    </option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div> 

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label><?=ucfirst('Expensas');?></label>
                            <select class="form-control" name="expensas[]" id="expensa_id" 
                            multiple="multiple" 
                            required="required" >
                        </select>
                    </div>
                </div>          
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="new_foto"><?=ucfirst('Comprobante');?></label>
                        <input type="file" class="form-control" id="comprobante"  name="comprobante" class="img-thumbnail" >
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="form-group">
                        <label for="detalle_id"><?=ucfirst('detalle');?></label>
                        <textarea class="form-control hint2basic" rows="6" id="detalle_id" placeholder="detalle" name="detalle"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-mk-primary">
                <i class="fa fa-plus"></i> Guardar
            </button>

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

        $( "#unidad_id" ).change(function() {
            var unidad_id = $(this).val();
            $("#expensa_id").html(' ');
            $.post(base_url+'/administrador/get_pagos_pendientes',
                {unidad_id:unidad_id},function(data){
                    var expensas = jQuery.parseJSON(data);
                    $.each(expensas, function(i, value) {
                        $("#expensa_id").append("<option value='"+value.id+"'>"+value.titulo+"</option>");
                    });
                    $('#expensa_id').multiSelect()
                });
        });

    })
</script>
