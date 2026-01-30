<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <h2 class="mc-page-header">Amenities</h2>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">      
        <div class="col-xs-12 col-sm-10">
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
        <div class="col-xs-12 col-sm-2">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/espacios_create" title="Crear Nueva">
                        <i class="fa fa-plus"></i>
                    </a>
                </li>
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

            <hr>
            <div class="mc-table-responsive" id="mc-table">
                <!-- table read goes here -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
   
    var admin = new MCAdmin({
        search : '',
        limit: 20,
        order_type: 'DESC',
        order_by: 'espacios.id',
        url: '<?=base_url();?>administrador/espacios_read',
        table: 'espacios'
    });
    var elemento = 'el espacios';

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
            document.location = '<?=base_url();?>administrador/espacios_excel' + admin.filters_url();
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