<style type="text/css">
    .panel-title{
        padding-top: 7.5px;
        font-size: 22px;
        color: #832b38;
        font-weight: bold;
    }
</style>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-5">
                <h2 class="mc-page-header">Listado de Espacios</h2>
            </div>
            <div class="col-xs-12 col-sm-5">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                       <!--
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="input-search">
                                <span class="input-group-btn">
                                    <button class="btn btn-mk-primary" type="button" id="btn-search"><i class="fa fa-search"></i></button>
                                </span>

                            </div>
                        </div>
                    -->
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <hr class="hr_subrayado">
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

<script type="text/javascript">
   
    var admin = new MCAdmin({
        search : '',
        limit: 20,
        order_type: 'DESC',
        order_by: 'espacios.nombre_espacio',
        url: '<?=base_url();?>propietarios/espacios_read',
        table: 'espacios'
    });

    $(document).ready(function (){
         $('.mc-table-responsive').css('max-height', $(window).height() - 160);

        
        admin.update();

       /* $('.delete').click(function (e){
            e.preventDefault();
            primary_key_value = $(this).attr('data-primary-key');
            if(window.confirm('Are you sure?')){
                location.href = '<?=base_url();?>propietarios/espacios_delete/' + primary_key_value
            }
        });*/
        
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
            document.location = '<?=base_url();?>propietarios/espacios_excel' + admin.filters_url();
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

    });//ready
</script>