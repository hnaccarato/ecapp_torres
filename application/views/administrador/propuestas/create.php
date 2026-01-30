<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Nuevas Votaciones</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/propuestas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">
        <div class="col-xs-12" style="background-color: #FFF !important; padding: 10px">
            <form method="POST" name="propuestas_form" id="propuestas_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="sector_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="" name="titulo">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <label for="fecha_ini_id"><?=ucfirst('fecha de inicio');?></label>
                        <div class="input-group date">
                            <input type="text" class="form-control datepicker"  id="fecha_ini_id" placeholder="Fecha de inicio" name="fecha_ini" autocomplete="off"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>                    
                    <div class="col-xs-12 col-sm-3">
                        <label for="fecha_fin_id"><?=ucfirst('fecha de cierre');?></label>
                        <div class="input-group date">
                            <input type="text" class="form-control datepicker"  id="fecha_fin_id" placeholder="fecha de  cierre" name="fecha_fin" autocomplete="off"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                        </div>
                    </div>


                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="descripcion_id"><?=ucfirst('descripcion');?></label>
                            <textarea class="form-control hint2basic" rows="6" id="descripcion_id" placeholder="descripcion" name="descripcion"></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div>
                            <label for="descripcion_id"><?=ucfirst('Opciones');?></label>
                            <div class="form-group">
                                <input type="text" name="opciones[]" class="form-control" placeholder="Ingrese nueva opcion">
                            </div>                            
                            <div class="form-group">
                                <input type="text" name="opciones[]" class="form-control" placeholder="Ingrese nueva opcion">
                            </div>
                        </div>
                        <div class="after-add-more">
                        </div>
                        <div class="copy hide">
                            <div class="control-group input-group" style="margin-top:10px">
                                <input type="text" name="opciones[]" class="form-control" placeholder="Ingrese una nueva opcion">
                                <div class="input-group-btn"> 
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Quitar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="margin: 20px 0px 20px 0px;">
                                <button class="btn btn-success add-more" type="button">
                                    <i class="glyphicon glyphicon-plus"></i> Nueva Opción
                                </button>          
                            </div>
                        </div>

                    </div>                    
                    <div class="col-xs-12 col-sm-6">
                        <div>
                            <label for="descripcion_id"><?=ucfirst('Archivos');?></label>
                            <div class="form-group">
                                <input type="file" name="file[]" class="form-control">
                            </div>

                            <div class="copy_file hide">
                                <div class="control-group input-group" style="margin-top:10px">
                                    <input type="file" name="file[]" class="form-control">
                                    <div class="input-group-btn"> 
                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Quitar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="after-add-file">
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="margin: 20px 0px 20px 0px;">
                                <button class="btn btn-success add-file" type="button"><i class="glyphicon glyphicon-plus"></i>Nuevo Archivo</button>
                            </div>
                        </div>
                    </div>


                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="autorizado"><?=ucfirst('Enviar votación a propietarios');?></label>
                            <select class="form-control" id="autorizacion_id" name="estado_id" required>
                                <option value="1">No</option>
                                <option value="5">Si</option>

                            </select>
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>

            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    elemento = "Archivo de circular";
    $(document).ready(function (){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

        $(".add-more").click(function(){ 
            var html = $(".copy").html();
            $(".after-add-more").before(html);
        });

        $("body").on("click",".remove",function(){ 
            $(this).parents(".control-group").remove();
        });    

        $(".add-file").click(function(){ 
            var html = $(".copy_file").html();
            $(".after-add-file").before(html);
        });



    })
</script>