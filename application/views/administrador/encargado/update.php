<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Edit - Personal</h2>
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
                    <div class="col-md-6 col-xs-12">
                       
                        <?php if(is_file(BASEPATH.'../upload/encargado/'.$values->foto)){?>
                            <a href="#" class="thumbnail"> <img 
                                data-src="holder.js/100%x180" style="height: 200px; width: 200px; display: block;" 
                                src="<?=base_url('/upload/encargado/'.$values->foto)?>" data-holder-rendered="true"> 
                            </a> 
                        <?php } ?>
                        <div class="form-group">
                            <label for="file_id"><?=ucfirst('Foto');?></label>
                            <input type="file" class="form-control" name="file" >
                        </div>
          
                    </div>      
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="cargo_id"><?=ucfirst('Cargos');?></label>
                            <select name="cargo_id"  class="form-control" >
                                    <option value="">Seleccione</option>
                                <?php foreach($cargos as $value){?>
                                    <option value="<?=$value->id?>" <?=($value->id == $values->cargo_id)? "selected":" " ?>><?=$value->nombre?></option>
                                <?php } ?>   
                            </select>
                        </div>
                    </div>                
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="nombre_id"><?=ucfirst('nombre');?></label>
                            <input type="text" class="form-control" id="nombre_id" placeholder="nombre" name="nombre" value="<?=$values->nombre;?>">
                        </div>
                    </div>                    
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="telefono"><?=ucfirst('teléfono / celular');?></label>
                            <input type="text" class="form-control" id="telefono" placeholder="telefono" name="telefono" value="<?=$values->telefono;?>">
                        </div>
                    </div>                    
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="email"><?=ucfirst('email');?></label>
                            <input type="text" class="form-control" id="email" placeholder="email" name="email" value="<?=$values->email;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="legajo_id"><?=ucfirst('legajo');?></label>
                            <input type="text" class="form-control" id="legajo_id" placeholder="legajo" name="legajo" value="<?=$values->legajo;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="horario_id"><?=ucfirst('Diá y horario');?></label>
                            <input type="text" class="form-control" id="horario_id" placeholder="horario" name="horario" value="<?=$values->horario;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="vacaciones_id"><?=ucfirst('vacaciones');?></label>
                            <input type="text" class="form-control" id="vacaciones_id" placeholder="vacaciones" name="vacaciones" value="<?=$values->vacaciones;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="art_id">
                                <?=ucfirst('art / seguro');?>&nbsp;
                                <?php if(!empty($values->art)){ ?>
                                    <a  href="<?=base_url("upload/encargado/".$values->art)?>" 
                                    target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga ART</span></a>
                                <?php } ?>
                            </label>
                            <input type="file" class="form-control" id="art_id" placeholder="art" name="art" value="<?=$values->art;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="seguros_id"><?=ucfirst('seguros');?></label>
                            <input type="text" class="form-control" id="seguros_id" placeholder="seguros" name="seguros" value="<?=$values->seguros;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="medicina_id"><?=ucfirst('Obra social');?></label>
                            <input type="text" class="form-control" id="medicina_id" placeholder="medicina" name="medicina" value="<?=$values->medicina;?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="ropa_id"><?=ucfirst('Uniforme');?></label>
                            <input type="text" class="form-control" id="ropa_id" placeholder="ropa" name="ropa" value="<?=$values->ropa;?>">
                        </div>
                    </div>

                 <div class="col-xs-12 col-sm-12">
                   <div class="form-group">
                     <label for="tarea"><?=ucfirst('tareas a relizar');?></label>
                     <textarea class="form-control hint2basic" rows="6" id="tarea" name="tarea"><?=$values->tarea?></textarea>
                   </div>
                 </div>  
                </div>
                <br/>

                <button type="submit" class="btn btn-mk-primary pull-left">Guardar</button>

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