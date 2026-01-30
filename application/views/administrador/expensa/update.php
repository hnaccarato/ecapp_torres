    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-7">
                <h2 class="mc-page-header">Editar - Expensa</h2>
            </div>
            <div class="col-xs-12 col-sm-5">
                <ul class="mc-page-actions">
                    <li>
                        <a type="button" href="<?=base_url();?>administrador/expensa_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="hr_subrayado">
        <div class="panel panel-default">
        <div class="panel-body">
        <hr>    
        <div class="row">      
            <div class="col-xs-12">
                <form method="POST" name="edificios_form" id="edificios_form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="nombre_id"><?=ucfirst('expensa funcional');?></label>
                                <input type="text" class="form-control" id="nombre_id" placeholder="nombre" name="nombre" value="<?=$values->name;?>">
                            </div>
                        </div>                  
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="departamento"><?=ucfirst('departamento');?></label>
                                <input type="text" class="form-control" id="departamento" placeholder="departamento" name="departamento" value="<?=$values->departamento;?>">
                            </div>
                        </div>                        
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="porc"><?=ucfirst('Porcentual Depto');?></label>
                                <input type="text" class="form-control" id="porc" placeholder="Porcentual Depto" name="porc" value="<?=$values->porc;?>">
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" class="btn btn-mk-primary pull-left">Guardar</button>

                </form>

            </div>
        </div>
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