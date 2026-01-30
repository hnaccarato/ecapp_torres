<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h2 class="mc-page-header">Listado de Expensas</h2>
            </div>
            <div class="col-xs-12 col-sm-7 col-sm-offset-2">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="input-search">
                                <span class="input-group-btn">
                                    <button class="btn btn-mk-primary" type="button" id="btn-search"><i class="fa fa-search"></i></button>
                                </span>

                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="search">Mostrando</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="input-limit" value="20">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-mk-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-right" id="limit-list">
                                        <li><a href="javascript:void(0)">20</a></li>
                                        <li><a href="javascript:void(0)">40</a></li>
                                        <li><a href="javascript:void(0)">80</a></li>
                                        <li><a href="javascript:void(0)">160</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0)">Todos</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3">
                <ul class="mc-page-actions">
                    <li><a type="button" href="<?=base_url();?>administrador/expensas_create/" title="Crear Nueva"><i class="fa fa-plus"></i></a></li>
                    <li><a type="button" href="javascript:void(0)" id="export-view" title="Exportar vista"><i class="fa fa-table"></i></a></li>
                    <li><a type="button" href="<?=base_url();?>administrador/expensas_excel" title="Exportar Todo"><i class="fa fa-file-excel-o"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <hr>
            <div class="col-xs-12">
                <div id="mc-table">
                    <!-- table read goes here -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="eBancarios">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formComprobante" enctype="multipart/form-data" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Gastos Mensuales</h4>
                </div>
                <div class="modal-body">
                    <div id="fountainG" >
                        <div id="fountainG_1" class="fountainG"></div>
                        <div id="fountainG_2" class="fountainG"></div>
                        <div id="fountainG_3" class="fountainG"></div>
                        <div id="fountainG_4" class="fountainG"></div>
                        <div id="fountainG_5" class="fountainG"></div>
                        <div id="fountainG_6" class="fountainG"></div>
                        <div id="fountainG_7" class="fountainG"></div>
                        <div id="fountainG_8" class="fountainG"></div>
                    </div>
                    <div class="alert alert-success alert-dismissible" id="alert" 
                        style="display: none">
                      <a class="close" data-dismiss="alert">×</a>
                      <strong>Archivo Guardado</strong> -  <strong>Puede seguir cargando comprobantes</strong>
                    </div>
                    <fieldset id="modal_fieldset">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="tipo_id">Categoria</label>
                                <select name="tipo_gasto_id" class="form-control" id="tipo_id" 
                                    required="required">
                                    <option value="" >Seleccione</option>
                                    <?php foreach ($tipo_gastos->result() as $value) {?>
                                        <option value="<?=$value->id?>"><?=$value->name?></option>
                                    <?php } ?>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="titulo">Titulo</label>
                                <input type="text" name="title[]" class="form-control" id="titulo" 
                                placeholder="Descripcion" >
                            </div>  
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="file">Comprobante</label>
                                <input type="file" name="comprobante[]" id="file" style="color: transparent" required="required" multiple="multiple" accept="image/*,.csv,.pdf,.docx,.doc,.xls,.xlsx">
                                <input type="hidden" name="recibo_id" id="recibo_id" >
                            </div>
                        </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="submit_upload">Guardar</button>
                </div>
            </fieldset>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="notificarExpensa">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form_notificar" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Notificar Expensas</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success alert-dismissible" id="alert_mensage" 
                        style="display: none">
                      <a class="close" data-dismiss="alert">×</a>
                      <strong>Archivo Guardado</strong>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="titulo">Seleccione unidad</label>
                                <select class="form-control" name="unidad_id[]" id="unidad_id" multiple="multiple" required="required">
                                    <option value="0">Todas Las Unidades</option>
                                    <?php foreach ($unidades->result() as $value) { ?>
                                        <option value="<?=$value->id?>">
                                            <?=$value->name?> - <?=$value->departamento?> 
                                        </option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="recibo_id" id="send_recibo_id" >
                            </div> 
                        </div>  
                        <!--
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="titulo">Titulo</label>
                                <input type="text" name="title" class="form-control" id="titulo" 
                                placeholder="Descripcion" required="required" >
                            </div> 
                        </div> 
                       
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea  name="descripcion" class="form-control" id="descripcion"></textarea>
                                <input type="hidden" name="recibo_id" id="send_recibo_id" >
                            </div>  
                        </div>
                        -->
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--
<div class="modal fade" tabindex="-1" role="dialog" id="newPago">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" id="form_newPago">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Informar Pago</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success alert-dismissible" id="alert_pago" 
                        style="display: none">
                      <a class="close" data-dismiss="alert">×</a>
                      <strong>Pago Informado</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-12">           
                            <div class="form-group">
                                <label for="exampleInputEmail1">Unidades</label>
                                  <select class="form-control" name="unidad_id" required="required">
                                      <option value="">Seleccione unidad</option>
                                      <?php foreach ($unidades->result() as $value) { ?>
                                          <option value="<?=$value->id?>">
                                              <?=$value->name?> - <?=$value->departamento?> 
                                          </option>
                                      <?php } ?>
                                  </select>
                           </div> 
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Descripción</label>
                                <textarea class="form-control hint2basic" rows="3" name="descripcion" required="required"></textarea>
                                <input type="hidden" name="recibo_id" id="pago_recibo_id" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Comprobante</label>
                                <input type="file" id="exampleInputFile" name="comprobante">
                                <p class="help-block">Adjuntar comprobante</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
-->

<script type="text/javascript">

    var admin = new MCAdmin({
        search : '',
        limit: 20,
        order_type: 'DESC',
        order_by: 'recibos.id',
        url: '<?=base_url();?>administrador/expensas_read',
        table: 'recibos'
    });

    var elemento = "la expensa";

    $(document).ready(function (){
        $("#fountainG").hide();

        $('.mc-table-responsive').css('max-height', $(window).height() - 160);
        admin.update();

        $('body').on( 'click','.notificar',function (e){
            e.preventDefault();
            var recibo_id = $(this).data('id');
            $("#send_recibo_id").val(recibo_id);
            $("#notificarExpensa").modal('show');
        });         

    /*    $('#unidad_id').multiselect({
            enableFiltering: true,
            maxHeight: 300,
            enableCaseInsensitiveFiltering: true
        });*/

        $('body').on( 'click','.newPago',function (e){
            e.preventDefault();
            var recibo_id = $(this).data('id');
            $("#pago_recibo_id").val(recibo_id);
            $("#newPago").modal('show');
        });

        $("body").on("submit","#form_notificar",function(e){
            e.preventDefault();
            var formData = new FormData(document.getElementById("form_notificar"));
            var recibo_id = $("#send_recibo_id").val();
            $.ajax({
                url:  base_url+"administrador/notificar/"+recibo_id,
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(data){
                document.getElementById("form_notificar").reset();
                $("#notificarExpensa").modal('hide');
                $('#form_notificar')[0].reset();
            });
        });          

        $("body").on("submit","#form_newPago",function(e){
            e.preventDefault();
            var formData = new FormData(document.getElementById("form_newPago"));
            $.ajax({
                url:  base_url+"/administrador/nuevos_pagos/",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(data){
                document.getElementById("form_newPago").reset();
                    $("#alert_pago").fadeTo(2000, 500).slideUp(500, function(){
                    $("#alert_pago").slideUp(500);
                });
            });
        });    


        $("body").on("submit","#formComprobante",function(e){
            e.preventDefault();
            var formData = new FormData(document.getElementById("formComprobante"));
            $.ajax({
                url:  base_url+"/administrador/add_gastos/",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#submit_upload").addClass('disabled');
                    $("#modal_fieldset").attr("disabled", true);
                    $("#fountainG").show();
                    
                },
                success: function(data) {
                    $("#submit_upload").removeClass('disabled');
                    $("#fountainG").hide();
                    $("#modal_fieldset").prop("disabled", false);
                    document.getElementById("formComprobante").reset();
                    $("#alert").fadeTo(2000, 14500).slideUp(14500, function(){
                        $("#alert").slideUp(14500);
                    });
                   // alert(data);
                }

            })
        });  

        $('body').on( 'click','.ebancarios',function (e){
            e.preventDefault();
            var recibo_id = $(this).data('id');
            $("#recibo_id").val(recibo_id);
            $("#eBancarios").modal('show');
        });

        $('#btn-search').click(function(){
            admin.search($('#input-search').val());
            admin.update();
        });

        $('#input-search').keypress(function(e) {
            if(e.which == 13) {
                admin.search($(this).val());
                admin.update();
            }
        });

        $('#limit-list>li>a').click(function(){
            $('#input-limit').val($(this).text());
            admin.limit($(this).text());
            admin.update();
        });

        $('#input-limit').keypress(function(e) {
            if(e.which == 13) {
                admin.limit($(this).val());
                admin.update();
            }
        });

        $('#export-view').click(function(){
            document.location = '<?=base_url();?>administrador/expensas_excel' + admin.filters_url();
        });

        $(document).on( 'click', 'td[data-column]', function() {
            if($(this).attr('data-order-type') == 'DESC'){
                $('td[data-column]').removeAttr('data-order-type');
                $(this).attr('data-order-type', 'ASC') 
            } else {
                $('td[data-column]').removeAttr('data-order-type');
                $(this).attr('data-order-type', 'DESC')   
            }
            admin.order($(this).attr('data-column'), $(this).attr('data-order-type'));
            admin.update();
        });

      /* inquilinos*/

});//ready
</script>