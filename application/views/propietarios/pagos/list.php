<style type="text/css">
    .modal {
      text-align: center;
      padding: 0!important;
    }
    .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
    }

    .modal-dialog {
      display: inline-block;
      text-align: left;
      vertical-align: middle;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-2">
            <h2 class="mc-page-header">Pagos</h2>
        </div>
        <div class="col-xs-12 col-sm-7">
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="search">Estados</label>
                        <select class="form-control" id="estado_id">
                            <option value="">Todos</option>
                            <?php foreach ($estados as $estado) { ?>
                                <option value="<?=$estado->id?>" >
                                    <?=$estado->nombre?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>  
                <div class="col-xs-12 col-sm-4">
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
                <div class="col-xs-12 col-sm-4">
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
                <li><a type="button" href="javascript:void(0)" title="Crear Nueva" id="nuevo_pago"><i class="fa fa-plus"></i></a></li>
                <li><a type="button" href="javascript:void(0)" id="export-view" title="Exportar vista"><i class="fa fa-table"></i></a></li>
                <li><a type="button" href="<?=base_url();?>propietarios/pagos_excel" title="Exportar Todo"><i class="fa fa-file-excel-o"></i></a></li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row" style="background-color:#FFFFFF;padding-top:30px">
        <div class="col-md-10 col-md-offset-1">
            <?php if($this->session->flashdata('message')):?>
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        <?=$this->session->flashdata('message')?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            <?php elseif($this->session->flashdata('error_message')):?>   
             <div class="col-md-12">
                 <div class="alert alert-warning" role="alert">
                     <?=$this->session->flashdata('error_message')?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
             </div>
            <?php else:  ?>    
            <div class="alert alert-success" role="alert">   
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p style="font-weight: bold; text-align: center;">El estado de su pago estará "acreditado" cuando el equipo de administración controle el comprobante de pago realizado.</p>
            </div>
            <?php endif;  ?>
        </div>
        <div class="col-xs-12">
            <div  id="mc-table">
                <!-- table read goes here -->
            </div>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="<?=base_url('propietarios/nuevos_pagos')?>" method="post" 
    enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Realizar Pagos</h4>
        </div>
        <div class="modal-body">
           <div class="form-group">
                <label for="exampleInputEmail1">Expensa</label>
                <select multiple="multiple"  id="recibos" name="recibos[]">
                    <?php foreach($recibos->result() as $value){?>
                        <option value="<?=$value->id?>"><?=$value->titulo?></option>
                    <?php } ?>
                </select>
           </div>           
           <div class="row">
               <div class="col-sm-12 col-md-6">
                   <div class="form-group">
                       <label for="exampleInputFile">Comprobante</label>
                       <input type="file" id="exampleInputFile" name="comprobante" 
                       accept="image/*,.pdf,.docx,.doc">
                       <p class="help-block">Adjuntar comprobante</p>
                   </div>
               </div>
               <div class="col-sm-12 col-md-6">
                   <div class="form-group">
                       <label for="exampleInputEmail1">Importe</label>
                       <input type="number" name="importe" class="form-control">
                   </div>    
               </div>
           </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_expensa" tabindex="-1" role="dialog" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ver expensa</h4>
        </div>
        <form id="form_expensa" enctype="multipart/form-data">
            <div class="modal-body" id="view_expensa">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
      </div>
    </div>
</div>

<script type="text/javascript">

var admin = new MCAdmin({
    search : '',
    limit: 20,
    order_type: 'DESC',
    order_by: 'pagos_users.id',
    url: '<?=base_url();?>propietarios/pagos_read',
    table: 'pagos_users'
});

$(document).ready(function (){
    $('.mc-table-responsive').css('max-height', $(window).height() - 160);


    admin.update();
   

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
        document.location = '<?=base_url();?>propietarios/pagos_excel' + admin.filters_url();
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



    $("#nuevo_pago").click(function(){
        $('#myModal').modal('show');
    });
    $('#recibos').multiSelect();

    $('body').on('click','.view_expensa',function (e){
        e.preventDefault();
        id = $(this).data('primary-key');
        $.post(base_url+'propietarios/view_pagos/',{id:id},function(data){
            $("#view_expensa").html(data);
            $("#modal_expensa").modal('show');
        })
    });  

    $('#estado_id').change(function(){
       var estado_id = this.value;
       admin.config.estado_id = this.value;
       admin.update();
    });

     $("body").on("submit","#form_expensa",function(e){
        e.preventDefault();
        var formData = new FormData(document.getElementById("form_expensa"));
        $.ajax({
            url:  base_url+"/propietarios/set_pago/",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(data){
            $("#modal_expensa").modal('hide'); 
            admin.update();
        });
    });

});//ready
</script>