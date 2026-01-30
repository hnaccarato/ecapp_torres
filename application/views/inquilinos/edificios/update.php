
<div class="panel panel-default">
    <div class="panel-heading"><strong>Mi Consorcio</strong></div>
    <div class="panel-body">
        <form method="POST" name="edificios_form" id="edificios_form" enctype="multipart/form-data">
            <div class="row">

                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="nombre_id"><?=ucfirst('nombre');?></label>
                        <input type="text" class="form-control" id="nombre_id" placeholder="nombre" name="nombre" value="<?=$values->nombre;?>">
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="telefono_id"><?=ucfirst('direccion');?></label>
                        <input type="text" class="form-control" id="direccion_id" placeholder="direccion" name="direccion" value="<?=$values->direccion;?>">
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="telefono_id"><?=ucfirst('telefono');?></label>
                        <input type="text" class="form-control" id="telefono_id" placeholder="telefono" name="telefono" value="<?=$values->telefono;?>">
                    </div>
                </div>                        


                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="new_foto"><?=ucfirst('Actualizar Foto');?></label>
                        <input type="file" class="form-control" id="new_foto" placeholder="imagen" name="imagen" value="<?=$values->imagen;?>"  class="img-thumbnail">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <label for="imagen_id"><?=ucfirst('Foto');?></label>
                    <a href="#" class="thumbnail">
                        <img src="<?=base_url('upload/edificios/'.$values->imagen)?>" alt="<?=$values->nombre;?>" id="imagen_id">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <div class="form-group">
                        <label for="imagen_id"><?=ucfirst('Forma de pago');?></label>
                        <textarea name="description" class="form-control"
                        rows="4" cols="80" required ><?=$values->description;?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Seleccione la posición del Mapa</div>
                        <div class="panel-body">
                            <label for="imagen_id"><?=ucfirst('Dirección');?></label>
                            <input type="text" id="tex_map"  class="form-control" name="direccion" value="<?=$values->direccion;?>">
                            <?php echo $map['html']; ?>
                        </div>
                        <input type="hidden" name="position" id="position" 
                        value="<?=$values->position?>" >
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-mk-primary pull-right">Submit</button>

        </form>
    </div>
</div>

<?php echo $map['js']; ?>
<script type="text/javascript">
    $(document).ready(function (){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
    })

    function set_position(data){
        $("#position").val(data);
    }

</script>