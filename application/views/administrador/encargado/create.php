<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Nuevo - Personal</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/encargado_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">      
        <div class="col-xs-12">
            <form method="POST" name="encargado_form" id="encargado_form"  enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="cargo_id"><?=ucfirst('Cargos');?></label>
                            <select name="cargo_id"  class="form-control" >
                                    <option value="">Seleccione</option>
                                <?php foreach($cargos as $value){?>
                                    <option value="<?=$value->id?>"><?=$value->nombre?></option>
                                <?php } ?>   
                            </select>
                        </div>
                    </div> 
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="legajo_id"><?=ucfirst('legajo');?></label>
                            <input type="text" class="form-control" id="legajo_id" placeholder="" name="legajo">
                        </div>
                    </div>                      
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="nombre_id"><?=ucfirst('nombre');?></label>
                            <input type="text" class="form-control" id="nombre_id" placeholder="" name="nombre">
                        </div>
                    </div>                    
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="nombre_id"><?=ucfirst('teléfono / celular');?></label>
                            <input type="text" class="form-control" id="Telefono_id" placeholder="" name="telefono">
                        </div>
                    </div>                    
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="nombre_id"><?=ucfirst('email');?></label>
                            <input type="text" class="form-control" id="email" placeholder="" name="email">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="horario_id"><?=ucfirst('Día y horario');?></label>
                            <input type="text" class="form-control" id="horario_id" placeholder="" name="horario">
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="vacaciones_id"><?=ucfirst('vacaciones');?></label>
                            <input type="text" class="form-control" id="vacaciones_id" placeholder="" name="vacaciones">
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="art_id"><?=ucfirst('art / Seguro');?></label>
                            <input type="file" class="form-control" id="art_id" placeholder="" name="art">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="medicina_id"><?=ucfirst('Obra Social');?></label>
                            <input type="text" class="form-control" id="medicina_id" placeholder="" name="medicina">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="ropa_id"><?=ucfirst('Uniforme');?></label>
                            <input type="text" class="form-control" id="ropa_id" placeholder="" name="ropa">
                        </div>
                    </div>                
              
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="file_id"><?=ucfirst('Foto');?></label>
                            <input type="file" class="form-control" name="file" >
                        </div>
                    </div>  
                              <div class="col-xs-12 col-sm-12">
                      <div class="form-group">
                        <label for="tarea"><?=ucfirst('tareas a relizar');?></label>
                        <textarea class="form-control hint2basic" rows="6" id="tarea" placeholder="tarea" name="tarea"></textarea>
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