<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-2">
            <h2 class="mc-page-header">Circulares</h2>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">      
        <div class="col-xs-12 col-sm-10">
            <div class="row">
                <div class="col-xs-12 col-sm-8">
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
        <div class="col-xs-12 col-sm-2">
            <ul class="mc-page-actions">
                <li><a type="button" href="<?=base_url();?>administrador/circular_create" title="Crear Nueva"><i class="fa fa-plus"></i></a></li>
                <li><a type="button" href="javascript:void(0)" id="export-view" title="Exportar vista"><i class="fa fa-table"></i></a></li>
                <li><a type="button" href="<?=base_url();?>administrador/circular_excel" title="Exportar Todo"><i class="fa fa-file-excel-o"></i></a></li>
            </ul>
        </div>
        <?php if($this->session->flashdata('erro_message')):?>
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                    <?=$this->session->flashdata('erro_message')?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php elseif($this->session->flashdata('message')):?>
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    <?=$this->session->flashdata('message')?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

            </div>
        <?php endif ;?>
        <div class="col-xs-12">
            <ul class="nav nav-tabs" id="nav_circular">
               
                <li class="selection active" data-estado_id='8'>
                    <a href="#">Activo</a>
                </li>
                <li class="selection" data-estado_id='6'>
                    <a href="#" data-fecha="<?date('Y-m-d')?>">Cerrado</a>
                </li>
                <li class="selection" data-estado_id='0' >
                    <a href="#">Todos</a>
                </li> 
            </ul>
            <div class="mc-table-responsive" id="mc-table">
                <!-- table read goes here -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="notificarExpensa">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="notificar" >
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
                            </div> 
                        </div>  
                        
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="titulo">Titulo</label>
                                <input type="text" name="title" class="form-control" id="titulo" 
                                placeholder="Descripcion" required="required">
                            </div> 
                        </div> 
                       
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea  name="descripcion" class="form-control" id="descripcion"></textarea>
                                <input type="hidden" name="recibo_id" id="send_recibo_id" >
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

<script type="text/javascript">

var estado_id;
var admin = new MCAdmin({
    search : '',
    limit: 20,
    order_type: 'DESC',
    order_by: 'circular.id',
    url: '<?=base_url();?>administrador/circular_read',
    table: 'circular'
});

$(document).ready(function (){

    admin.config.estado_id = 8;
    
    $('.mc-table-responsive').css('max-height', $(window).height() - 160);

    admin.update();

    $('.delete').click(function (e){
        e.preventDefault();
        primary_key_value = $(this).attr('data-primary-key');
        if(window.confirm('Esta por eliminar una circular')){
            location.href = '<?=base_url();?>administrador/circular_delete/' + primary_key_value
        }
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
        document.location = '<?=base_url();?>propietarios/circular_excel' + admin.filters_url()+'&estado_id='+estado_id;
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

    $('.selection').click(function(e){
        e.preventDefault();
        $("#nav_circular li").removeClass('active');
        $(this).addClass('active');
        estado_id = $(this).data('estado_id');
        admin.config.estado_id = estado_id;
        admin.update();
    });  

});//ready
</script>