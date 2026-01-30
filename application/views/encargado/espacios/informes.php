<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="mc-page-header">Informes de espacios</h2>
        </div>
        <div class="col-xs-12 col-sm-9">
            <div class="row">
                <div class="col-xs-12 col-sm-3">
                     <div class="form-group">
                        <label for="espacio_id">Espacios</label>
                        <select class="form-control" name="espacio_id" id="espacio_id">
                                <option value="0">Todos</option> 
                            <?php foreach ($espacios->result() as $espacio) {?>
                                <option value="<?=$espacio->id?>"><?=$espacio->nombre_espacio?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>    
                <div class="col-xs-12 col-sm-3">
                  <div class="form-group">
                    <label for="fecha_desde"><?=ucfirst('Fecha Desde');?></label>
                    <div class="input-group date">
                      <input type="text" class="form-control datepicker"  id="fecha_desde" value="<?=date_first_month_day()?>" placeholder="fecha desde" name="fecha_desde"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    </div>
                  </div>
                </div>          
                <div class="col-xs-12 col-sm-3">
                  <div class="form-group">
                    <label for="fecha_hasta"><?=ucfirst('Fecha Hasta');?></label>
                    <div class="input-group date">
                      <input type="text" class="form-control datepicker"  id="fecha_hasta"  placeholder="fecha Hasta" name="fecha_hasta" value="<?=date_last_month_day()?>"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    </div>
                  </div>
                </div>   
                <div class="col-xs-12 col-sm-3">
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
                <li>
                    <a type="button" href="javascript:void(0)" id="btn-search" title="Buscar resultado"><i class="fa fa-search" aria-hidden="true"></i></a>
                </li>                
                <li>
                    <a type="button" href="javascript:void(0)" id="export-view" title="Exportar vista"><i class="fa fa-table"></i></a>
                </li>
                <li>
                    <a type="button" title="Exportar Todo" id="export_excel"><i class="fa fa-file-excel-o"></i></a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12">
            <ul class="nav nav-tabs" id="nav_circular">             
                <li class="active selection" data-metodo='reservados'>
                    <a href="#">Activo</a>
                </li>
                <li class="selection" data-metodo='rechasados'>
                    <a href="#">Rechazados</a>
                </li>
            </ul>
            <div class="mc-table-responsive" id="mc-table">
                <!-- table read goes here -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var metodo = 'reservados';
    var metodo_excel = 'reservados_excel';
    
    var admin = new MCAdmin({
        search : '',
        limit: 20,
        order_type: 'DESC',
        order_by: 'reservas.id',
        url: '<?=base_url();?>encargado/'+metodo,
        table: 'espacios'
    });

    $(document).ready(function (){

        $('.mc-table-responsive').css('max-height', $(window).height() - 160);

        admin.update();

        $('#btn-search').click(function(){
            admin.config.espacio_id = $("#espacio_id").val();
            admin.config.fecha_desde = $("#fecha_desde").val();
            admin.config.fecha_hasta = $("#fecha_hasta").val();
            admin.update();
        });

        $('#limit-list>li>a').click(function(){
            $('#input-limit').val($(this).text());
            admin.limit($(this).text());
            admin.update();
        });

        $('#export-view').click(function(){
            document.location = '<?=base_url();?>encargado/'+metodo_excel+'/' + admin.filters_url();
        });        

        $('#export_excel').click(function(){
            document.location = '<?=base_url();?>encargado/'+metodo_excel;
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

        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
        
        $('.selection').click(function(e){
            
            e.preventDefault();
            $("#nav_circular li").removeClass('active');
            $(this).addClass('active');
            var metodo = $(this).data('metodo');
            metodo_excel = metodo+'_excel';
            admin.url('<?=base_url();?>encargado/'+metodo);

            admin.config.espacio_id = $("#espacio_id").val();
            admin.config.fecha_desde = $("#fecha_desde").val();
            admin.config.fecha_hasta = $("#fecha_hasta").val();
            
            admin.update();
        }); 

    });
</script>