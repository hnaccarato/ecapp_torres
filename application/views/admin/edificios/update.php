    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 title">
                <div class="col-xs-12 col-sm-7">
                    <h2 class="mc-page-header">Edit - Edificios</h2>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <ul class="mc-page-actions">
                        <li>
                            <a type="button" href="<?=base_url();?>admin/edificios_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-xs-12">
                <form method="POST" name="edificios_form" id="edificios_form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="nombre_id"><?=ucfirst('nombre');?></label>
                                <input type="text" class="form-control" id="nombre_id" placeholder="nombre" name="nombre" value="<?=$values->nombre;?>" required="required">
                            </div>
                        </div>                        
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="direccion"><?=ucfirst('Dirección');?></label>
                                <input type="text" class="form-control" id="direccion"  
                                name="direccion" value="<?=$values->direccion;?>" required="required" >
                            </div>
                        </div>
      
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="telefono_id"><?=ucfirst('telefono');?></label>
                                <input type="text" class="form-control" id="telefono_id" placeholder="telefono" name="telefono" value="<?=$values->telefono;?>" 
                                required="required">
                            </div>
                        </div>                        

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="imagen_id"><?=ucfirst('imagen');?></label>
                                <input type="file" class="form-control" id="imagen_id" 
                                name="imagen" value="" >
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                          <a href="#" class="thumbnail">
                            <img src="<?=base_url('upload/edificios/'.$values->imagen)?>" alt="<?=$values->nombre;?>">
                          </a>
                        </div>
                        <div class="col-md-offset-6"></div>
                    </div>
                    <div class="row">

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="telefono_id"><?=ucfirst('empresas');?></label>
                                <select name="empresa_id" class="form-control" id="empresa_id" required="required">
                                    <option value=" ">Seleccione</option>
                                    <?php foreach ($empresas as $key => $value) { ?>

                                        <option value="<?=$value['id']?>"  <?=($value['id'] == $values->empresa_id)? "selected":" " ?> ><?=$value['nombre']?></option>
                                    <?php } ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="categoria_id"><?=ucfirst('Permisos');?></label>
                                <select class="form-control" id="categoria_id" 
                                name="categoria_id" required="required">
                                    <option value=" ">Seleccione</option>
                                    <?php foreach ($categorias->result() as $categoria) { ?>
                                           
                                            <option value="<?=$categoria->id?>"
                                                 <?=($categoria->id == $values->categoria_id)? "selected":" " ?> ><?=$categoria->nombre?></option>                          
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <label for="imagen_id"><?=ucfirst('Forma de pago');?></label>
                                <textarea name="description" class="form-control hint2basic" required="required" rows="10" ><?=$values->description;?></textarea>
                            </div>
                        </div>
                    </div>
    <!--
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">Seleccione la posición del Mapa</div>
                                <div class="panel-body">
                                    <label for="imagen_id"><?=ucfirst('Busque la direccion dirección');?></label>
                                   <input type="text" id="tex_map" class="form-control" value="<?=$values->direccion;?>">
                                    <?php echo $map['html']; ?>
                                </div>
                                <input type="hidden" name="position" id="position" 
                                  value="<?=$values->position?>" >
                            </div>
                        </div>
                    </div>
                    -->

                    <h4>Credenciales Mercado Pago</h4>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="mode"><?=ucfirst('mode');?></label>
                                <input type="text" class="form-control" id="mode" placeholder="Mode" name="mode"
                                 value="<?=$values->mode;?>">
                            </div>
                        </div>                    
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="ci"><?=ucfirst('ci');?></label>
                                <input type="text" class="form-control" id="ci" placeholder="ci" name="ci" 
                                value="<?=$values->ci;?>">
                            </div>
                        </div>                    
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="cs"><?=ucfirst('cs');?></label>
                                <input type="text" class="form-control" id="cs" placeholder="CS" name="cs" 
                                value="<?=$values->cs;?>">
                            </div>
                        </div>                    
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="public_key_sandbox"><?=ucfirst('public key sandbox');?></label>
                                <input type="text" class="form-control" id="public_key_sandbox" placeholder="public key sandbox" name="public_key_sandbox" value="<?=$values->public_key_sandbox;?>">
                            </div>
                        </div>                    
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="access_token_sandbox"><?=ucfirst('Access Token Sandbox');?></label>
                                <input type="text" class="form-control" id="access_token_sandbox" placeholder="Access Token Sandbox" name="access_token_sandbox" value="<?=$values->access_token_sandbox;?>">
                            </div>
                        </div>                    
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="public_key_production"><?=ucfirst('public key production');?></label>
                                <input type="text" class="form-control" id="public_key_production" placeholder="Public Key Production" name="public_key_production" value="<?=$values->public_key_production;?>">
                            </div>
                        </div>                    

                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="access_token_production"><?=ucfirst('access token production');?></label>
                                <input type="text" class="form-control" id="access_token_production" placeholder="Access Token Production" name="access_token_production" value="<?=$values->access_token_production;?>">
                            </div>
                        </div>                        
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="porcentaje"><?=ucfirst('porcentaje');?></label>
                                <input type="text" class="form-control" id="porcentaje" placeholder="Access Token Production" name="porcentaje" value="<?=$values->porcentaje;?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-mk-primary pull-right">Submit</button>

                </form>

            </div>
        </div>
    </div>  
    <?php //echo $map['js']; ?>
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