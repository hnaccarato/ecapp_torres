<style type="text/css">
    
    .btntop{
        margin-top: 23px;
        display: none;
    }

    .file_add{
       border: 1px solid silver;
       padding: 12px;
       display: flow-root;
       margin-bottom: 23px; 
    }


</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header"><?=$title?></h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url('administrador/legales_list/'.$values->tipo_legal_id);?>"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">  
        <div class="col-xs-12">
            <form method="POST" name="seguros_form" id="seguros_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="name_id"><?=ucfirst('Titulo');?></label>
                            <input type="text" class="form-control" id="name_id" placeholder="Titulo de Seguro" 
                            name="titulo" value="<?=$values->titulo?>">
                        </div>
                    </div>                    

                <?php if ($values->tipo_legal_id == JUCIOS): ?>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="abogado"><?=ucfirst('Abogado');?></label>
                                <input type="text" class="form-control" id="abogado" placeholder="Nombre del Abogado" 
                                name="abogado" value="<?=$values->abogado?>">
                            </div>
                        </div> 

                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="legajo"><?=ucfirst('Legajo');?></label>
                                <input type="text" class="form-control" id="legajo" placeholder="Numero de Legajo" 
                                name="legajo" value="<?=$values->legajo?>">
                            </div>
                        </div>
                <?php endif; ?> 
            </div>
            <section class="file_add">
                <div class="container_div">
                    <div class="original_div" id="dv">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5">
                                <div class="form-group">
                                    <label for="abogado" class="lavel"><?=ucfirst('Nombre archivo');?></label>
                                    <input type="text" class="form-control name_file"  placeholder="Nombre archivo" 
                                    name="name_legal[]" >
                                </div>
                            </div> 
                            <div class="col-xs-12 col-sm-5">
                                <div class="form-group">
                                    <label for="file_id" class="lavel" ><?=ucfirst('Archivo');?></label>
                                    <input type="file" class="form-control" name="file[]" >
                                </div>
                            </div>                    
                            <div class="col-xs-12 col-sm-2">
                                <a href="#" class="btn btn-danger delete_file btntop" title="Eliminar archivo">
                                    <i class="fa fa-times"></i> Eliminar
                                </a>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <a id="btn" class="btn btn-mk-primary pull-right">
                        <i class="fa fa-plus"></i> Agregar archivo
                    </a>
                </div>
            </section>

            <div class="row" id="load_images" style=" margin-top: 25px;">

            </div>
            <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
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
        get_image();
    })
    

    $(function() {
        
        $(document).on('click','.delete_file',function(){
            $(this).parent().parent().remove();
        });

        $('#btn').on('click', function() {
            var div_copy = $('#dv').clone();
            div_copy.children().find(".form-control").val("");
            div_copy.children().find(".lavel").remove();
            div_copy.children().find(".delete_file").removeClass('btntop');
            $('.container_div').append(div_copy);
        });

        $(document).on('click','.remove_image',function(e){
            e.preventDefault();
            var file_id = $(this).data('id');
            var legal_id = <?=$values->id?>;
            var text_success = 'Eliminado!';
            var url = base_url+'administrador/remove_file';
            Swal.fire({
                title: "Desea eliminar el archivo ?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498DB',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar'

            }).then((result) => {
                if (result.value) {
                    Swal.fire(
                        text_success,
                        '',
                        'success'
                    )
                    $.post(url,{legal_id:legal_id,file_id:file_id},function(){
                           $("#"+file_id).remove();
                    });
                }
            })

            $("#"+id).remove();
        });
    });

    function get_image(){
        $.get(base_url+'administrador/load_file/<?=$values->id?>', function( data ) {
            $( "#load_images" ).html( data );
        });
    }
</script>