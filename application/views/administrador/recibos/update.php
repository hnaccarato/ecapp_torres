<style type="text/css">
.panel-heading{
    text-align: center;
    font-weight: 800;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Edicion de Expensas</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/expensas_list/">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">
    
    <div class="row">
        <div class="col-xs-12">
            <form method="POST" name="recibos_form" id="recibos_form" enctype="multipart/form-data" autocomplete="off">
                
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="titulo" name="titulo" value="<?=$values->titulo;?>" required>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('fecha');?></label>
                            <input type="text" class="form-control datepicker" id="fecha_id" placeholder="fecha" name="fecha" value="<?=$values->fecha;?>" required>
                        </div>
                    </div>
                 
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="descripcion_id"><?=ucfirst('descripcion');?></label>
                            <textarea type="text" rows="10" class="form-control hint2basic" id="descripcion_id" name="descripcion" ><?=$values->descripcion;?></textarea>
                        </div>
                    </div>                  
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="col-xs-12 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">Expensas</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <input type="file" class="form-control" id="file_id" placeholder="file" name="file" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <?php if(!empty($values->file)){?>
                                            <a  href="<?=base_url("upload/expensas/".$values->file)?>" 
                                                target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Prorrateo</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <input type="file" class="form-control" id="prorrateo" placeholder="prorrateo" name="prorrateo" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <?php if(!empty($values->prorrateo)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->prorrateo)?>" 
                                                    target="_blank">
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Gastos Particulares</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="gparticulares" 
                                                name="gparticulares" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php if(!empty($values->gparticulares)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->gparticulares)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Rendición de cuentas auditadas</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="anexo1" 
                                                name="anexo1" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php if(!empty($values->anexo1)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->anexo1)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>                            

                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Estado de situación patrimonial</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="anexo2" 
                                                name="anexo2" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php if(!empty($values->anexo2)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->anexo2)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Extracto Bancario</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="ebancarios" 
                                                name="ebancarios" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php if(!empty($values->ebancarios)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->ebancarios)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>  
                            <div class="col-xs-12 col-sm-6">  
                              <div class="form-group">
                                <label for="autorizacion_id">
                                    <?=ucfirst('Mostrar a propietarios');?>  <p>
                                        <?=($values->estado_id == ENVIADO)? "Esta expensa ya fue notificada, si no desea quitarla no seleccioné nada":"Esta expensa no fue notificada desea hacerlo a hora? ";?>
                                    </p>
                                        
                                    </label>
                                <select class="form-control" id="autorizacion_id" name="estado_id" >
                                    <option value="-1" <?=($values->estado_id == 0)? "selected":" " ?>>Seleccione</option>
                                    <option value="<?=PENDIENTE?>">No</option>
                                    <option value="<?=ENVIADO?>">Si</option>
                                    <option value="9">Sin Notificar</option>
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
        $(document).ready(function (){
            $('.datepicker').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });
        })
    </script>