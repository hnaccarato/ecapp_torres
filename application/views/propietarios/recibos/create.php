<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Nueva Expensa</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>propietarios/expensas_list/">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <hr>
    <form method="POST" name="recibos_form" id="recibos_form" enctype="multipart/form-data" autocomplete="off">
        <div class="row">    
            <div class="col-xs-12">

                <div class="row">

                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('fecha');?></label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker"  id="fecha_id" placeholder="fecha" name="fecha"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="" name="titulo" required>    
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="descripcion_id"><?=ucfirst('descripcion');?></label>
                            <textarea type="text" rows="10" class="form-control hint2basic" id="descripcion_id" name="descripcion"></textarea>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
        <div class="panel panel-default">
            <div class="panel-body">    

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="file_id"><?=ucfirst('Expensas');?></label>
                        <input type="file" class="form-control" name="file" >
                    </div>
                </div>                    
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="prorrateo"><?=ucfirst('prorrateo');?></label>
                        <input type="file" class="form-control" name="prorrateo" >
                    </div>
                </div>                    
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="lsueldo"><?=ucfirst('Libro de Sueldos');?></label>
                        <input type="file" class="form-control" name="lsueldo" id="lsueldo" >
                    </div>
                </div>                    
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="ebancarios"><?=ucfirst('Extracto Bancario');?></label>
                        <input type="file" class="form-control" name="ebancarios" id="ebancarios">
                    </div>
                </div>                
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="anexo1"><?=ucfirst('Anexo I');?></label>
                        <input type="file" class="form-control" name="anexo1" id="anexo1">
                    </div>
                </div>                
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="anexo2"><?=ucfirst('Anexo II');?></label>
                        <input type="file" class="form-control" name="anexo2" id="anexo2">
                    </div>
                </div>                  
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="gparticulares"><?=ucfirst('Gastos Particulares');?></label>
                        <input type="file" class="form-control" name="gparticulares" id="gparticulares">
                    </div>
                </div>               
                 <div class="col-xs-12 col-sm-6">
                </div>
            </div>
            <br/>
        </div>
        <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
    </form>
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