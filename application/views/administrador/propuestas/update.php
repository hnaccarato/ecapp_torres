
<div class="row">
    <div class="col-xs-12 col-sm-7">
        <h2 class="mc-page-header">Editar - Votacion</h2>
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
<div class="container-fluid"  style="background-color: #FFF !important; padding: 10px 20px 10px 20px">    
    <div class="row">  
        <div class="col-xs-12" >
            <form method="POST" name="propuestas_form" id="propuestas_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="titulo_id"><?=ucfirst('titulo');?></label>
                                <input type="text" class="form-control" id="titulo_id" placeholder="titulo" name="titulo" value="<?=$values->titulo;?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">    
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="fecha_ini_id"><?=ucfirst('fecha de inicio');?></label>
                                <input type="text" class="form-control datepicker" id="fecha_ini_id" placeholder="Fecha de inicio" name="fecha_ini" value="<?=$values->fecha_ini;?>" autocomplete="off">
                            </div>
                        </div>                        
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="fecha_fin_id"><?=ucfirst('fecha de cierre');?></label>
                                <input type="text" class="form-control datepicker" id="fecha_fin_id" placeholder="Fecha de cierre" name="fecha_fin" value="<?=$values->fecha_fin;?>" autocomplete="off">
                            </div>
                        </div>


                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <label for="descripcion_id"><?=ucfirst('descripcion');?></label>
                                <textarea class="form-control hint2basic" rows="6" id="descripcion_id" placeholder="descripcion" name="descripcion"><?=$values->descripcion;?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <label for="descripcion_id"><?=ucfirst('Opciones');?></label>

                            <div>
                                <div class="form-group">
                                    <input type="text" name="opciones[]" class="form-control" placeholder="Ingrese nueva opcion"  >
                                </div>
                            </div>
                            <?php foreach ($opciones->result() as  $value) { ?>

                            <div class="control-group input-group" style="margin-top:10px">
                                <input type="text" name="opciones[]" 
                                        class="form-control" 
                                        placeholder="Ingrese una nueva opcion"
                                        value="<?=$value->titulo?>">
                                <div class="input-group-btn"> 
                                  <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Quitar</button>
                                </div>
                            </div>
                            <?php } ?>
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
                            <label for="descripcion_id"><?=ucfirst('Archivos');?></label>
                            <div>
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
                                <br>
                            </div>
                            <?php $c = 1; ?>
                            <?php foreach ($files->result() as  $value) { ?>
                               <div class="form-group" id="file_<?=$value->id?>">
                                   <p class="form-control">Descargar opción <?=$c++?>
                                    <span class="opcion_file">
                                        <a href="<?=base_url('/upload/propuesta/'.$value->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                                        <a href="<?=base_url('administrador/delete_file_propuesta/'.$value->id)?>" target="_blank" class="delete_file" data-id="<?=$value->id?>"> <i class="fa fa-times"></i></a>
                                    </span>   
                                   </p> 
                               </div>
                            <?php } ?>
                            <div class="after-add-file">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin: 20px 0px 20px 0px;">
                                    <button class="btn btn-success add-file" type="button"><i class="glyphicon glyphicon-plus"></i>Nuevo Archivo</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-xs-12 col-sm-6">
                          <div class="form-group">
                            <label for="autorizacion_id">
                                <?=ucfirst('Enviar votación a propietarios');?></label>
                            <select class="form-control" id="autorizacion_id" name="estado_id" 
                            required>
                                <option value="5" <?=($values->estado_id == 5)? "selected":" " ?>>Si</option>
                                <option value="1"  <?=($values->estado_id == 1)? "selected":" " ?>>No</option>
                            </select>
                          </div>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-mk-primary pull-right">Guardar</button>

            </form>

        </div>
    </div>
</div>  

<script type="text/javascript">
var elemento = "El archivo";

$(document).ready(function (){
    $('.datepicker').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });

    $(".add-more").click(function(){ 
         var html = $(".copy").html();
         $(".after-add-more").after(html);
     });

     $("body").on("click",".remove",function(){ 
         $(this).parents(".control-group").remove();
     });    

     $(".add-file").click(function(){ 
         var html = $(".copy_file").html();
         $(".after-add-file").after(html);
     });

    $(document).on('click','.delete_file',function(e){

        e.preventDefault();

        var id = $(this).data('id');

        var url = $(this).attr('href');
        var text = 'Desea eliminar '+elemento+'?';
        var text_success = elemento+' eliminado!';
         
        Swal.fire({
             title: text,
             type: 'question',
             showCancelButton: true,
             confirmButtonColor: '#3498DB',
             cancelButtonColor: '#d33',
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Aceptar'

        }).then((result) => {
             if (result.value) {
                 Swal.fire(
                     text_success,
                     '',
                     'success'
                 )
                 $.get(url,function(){
                        $("#file_"+id).remove();
                 });
             }
        })

    });

})
</script>