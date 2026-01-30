<div class="container-fluid">
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
            </div>
        </div>

        <div class="col-xs-12 col-sm-2">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>admin/edificios_create" title="create new">
                        <i class="fa fa-plus"></i>
                    </a>
                </li>
                <li>
                    <a type="button" 
                        href="javascript:void(0)" id="export-view" title="export current view">
                        <i class="fa fa-table"></i>
                    </a>
                </li>
                <li>
                    <a type="button" href="<?=base_url();?>admin/edificios_excel" 
                        title="export full table">
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12">
            <div id="mc-table">
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
        order_by: 'edificios.id',
        url: '<?=base_url();?>admin/edificios_read',
        table: 'edificios'
    });

    $(document).ready(function (){

        $( "body" ).on( "click", ".list_pag a", function(event) {
            event.preventDefault();
            var href = $(this).attr('href');
            $( "#mc-table" ).load( href );

        });

        $("body").on('click',".delete",function(event){
            event.preventDefault();
            var href = $(this).attr('href');
            var id = $(this).data('id');
            $.confirm({
                content: "",
                title: "Realmente desea eliminar el consorcio ?",
                confirmButton: "Yes",
                cancelButton: "No",
                confirmButtonClass: "btn-warning",
                cancelButtonClass: "btn-default",
                confirm: function() {
                   $.get(href,function(){
                        $("#"+id).remove();
                    });
                },
                cancel: function() {
                    // nothing to do
                }
            });


        //    $( "#mc-table" ).load( href );

        });

         $('.mc-table-responsive').css('max-height', $(window).height() - 160);

        admin.update();

    /*    $('.delete').click(function (e){
            e.preventDefault();
            primary_key_value = $(this).attr('data-primary-key');
            if(window.confirm('Are you sure?')){
                location.href = '<?=base_url();?>admin/edificios_delete/' + primary_key_value
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
            document.location = '<?=base_url();?>admin/edificios_excel' + admin.filters_url();
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
