<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Nueva Expensa</h2>
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

    <form method="POST" name="recibos_form" id="recibos_form" enctype="multipart/form-data" autocomplete="off">
        <div class="row">    
            <div class="col-xs-12">

                <div class="row">

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="descripcion_id"><?=ucfirst('titulo');?></label>
                             <input type="detalle" class="form-control" placeholder="titulo" name="titulo">
                        </div>
                    </div>  

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('fecha');?></label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker"  id="fecha_id" placeholder="fecha" name="fecha" required="required"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
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

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="file_id"><?=ucfirst('Expensas');?></label>
                        <input type="file" class="form-control" name="file" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>                    
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="prorrateo"><?=ucfirst('prorrateo');?></label>
                        <input type="file" class="form-control" name="prorrateo" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>    
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="gparticulares"><?=ucfirst('Gastos Particulares');?></label>
                        <input type="file" class="form-control" name="gparticulares" id="gparticulares" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>  
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="anexo1"><?=ucfirst('Rendición de cuentas auditadas');?></label>
                        <input type="file" class="form-control" name="anexo1" id="anexo1" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>                
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="anexo2"><?=ucfirst('Estado de situación patrimonial');?></label>
                        <input type="file" class="form-control" name="anexo2" id="anexo2" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>                                   
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="ebancarios"><?=ucfirst('Extracto Bancario');?></label>
                        <input type="file" class="form-control" name="ebancarios" id="ebancarios" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                    </div>
                </div>  
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group">
                    <label for="autorizado"><?=ucfirst('Enviar a propietarios');?></label>
                    <select class="form-control" id="autorizacion_id" name="estado_id" required>
                        <option value="0">No</option>
                        <option value="<?=ENVIADO?>">Si</option>
                    </select>
                  </div>
                </div>    
                <!--          
                  <div class="col-xs-12 col-md-6">
                      <div class="form-group">
                          <label for="lsueldo"><?=ucfirst('Libro de Sueldos');?></label>
                          <input type="file" class="form-control" name="lsueldo" id="lsueldo" accept=".csv,.pdf,.docx,.doc,.xls,.xlsx">
                      </div>
                  </div>   
               -->
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