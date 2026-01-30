<style type="text/css">
    .gastos_titulo{
        background-color: gainsboro;
        display: flex;
        margin-bottom: 18px;
        margin-top: 3px;
    }
    .gastos{
        padding: 18px;
    }

    .comprobante{
        margin-top: 5px;
        border-bottom: 3px gainsboro solid;
        margin-right: 5px;
    }

</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Expensas: <?=$values->titulo;?></h2>
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

    <div class="panel panel-default">
        <div class="panel-heading panel-heading-custom">
            <h3 class="panel-title"><?=$values->titulo;?></h3>
        </div>
        <div class="panel-body">
            <h4>Descripción: </h4>
            <p><?=$values->descripcion;?></p>
            <hr>
            <div class="row">

                <?php if(!empty($values->file)){?>
                    <div class="col-md-4">
                        <div class="col-md-8"><p><strong>Liquidación de Expensas</strong></p></div>
                        <div class="col-md-4">                                            
                            <a  href="<?=base_url("upload/expensas/".$values->file)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>  
                    </div>  
                <?php } ?>  

                <?php if(!empty($values->prorrateo)){?>     
                    <div class="col-md-4">      
                        <div class="col-md-8"><p><strong>Prorrateo de Expensas</strong></p></div>
                        <div class="col-md-4">                                            
                            <a  href="<?=base_url("upload/expensas/".$values->prorrateo)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>      
                    </div>      
                <?php } ?>

                <?php if(!empty($values->gparticulares)){?>
                    <div class="col-md-4"> 
                        <div class="col-md-8"><p><strong>Gastos Particulares</strong></p></div>
                        <div class="col-md-4 pull-left">                                            
                            <a  href="<?=base_url("upload/expensas/".$values->gparticulares)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>
                    </div>
                <?php } ?>

                <?php if(!empty($values->anexo1)){?>
                    <div class="col-md-4"> 
                        <div class="col-md-8"><p><strong>Rendición de cuentas auditadas</strong></p></div>
                        <div class="col-md-4">                                              
                            <a  href="<?=base_url("upload/expensas/".$values->anexo1)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>  
                    </div>
                <?php } ?>

                <?php if(!empty($values->anexo2)){?>
                    <div class="col-md-4"> 
                        <div class="col-md-8"><p><strong>Estado de situación patrimonial</strong></p></div>
                        <div class="col-md-4">                                            
                            <a  href="<?=base_url("upload/expensas/".$values->anexo2)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>
                    </div>
                <?php } ?>
<!--
                <?php if(!empty($values->lsueldo)){?>
                    <div class="col-md-4">
                        <div class="col-md-8"><p><strong>Libro de Sueldos</strong></p></div>
                        <div class="col-md-4">                                            
                            <a  href="<?=base_url("upload/expensas/".$values->lsueldo)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                        </div>
                    </div>
                <?php } ?>
-->
                <?php if(!empty($values->lsueldo)){?>   
                    <div class="col-md-4">
                        <div class="col-md-8"><p><strong>Extracto Bancario</strong></p></div>
                        <div class="col-md-4">                                            
                            <?php if(!empty($values->ebancarios)){?>
                            <a  href="<?=base_url("upload/expensas/".$values->ebancarios)?>" 
                            target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                            <?php } ?>
                        </div>  
                    </div>
                <?php } ?>
            </div>  

            <hr>
            <?php if($gastos->num_rows() > 0){?>
            <h3>
        
                    <strong>Detalles de Gastos</strong> 
                    <span class="small"><?=$values->titulo;?></span>
               
            </h3>
            <?php } ?>
            <div class="row gastos">
                <?php $flag = 0; ?>
                <?php foreach ($gastos->result() as $gasto) { 
                    if($flag != $gasto->tipo_gasto_id){
                        echo "<div class='col-md-12 gastos_titulo'><hr><h4><strong>".$gasto->tipo."</strong></h4></div>";
                    } ?>
                    <?php if(!empty($gasto->comprobante)){?>
                        <div class="col-md-12" id="<?=$gasto->id?>"">
                            <div class="row comprobante">
                                <div class="col-md-6"><p>
                                    <strong><?=ucfirst(strtolower($gasto->titulo))?></strong>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <a  href="<?=base_url("upload/gastos/".$gasto->comprobante)?>" 
                                    target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file" title="<?=ucfirst(strtolower($gasto->titulo))?>">Descarga</span></a>
                                </div>
                                <div class="col-md-2"> 
                                    <button type="button" class="btn btn-danger com_delete" data-id="<?=$gasto->id?>">X</button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php $flag = $gasto->tipo_gasto_id;?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
      /*  $(".com_delete").click(function(e){
            e.preventDefault();
            var comprobante_id = $(this).data('id');
            var text = "Desea eliminar el comprobante";
            var url = base_url+"administrador/expensas_delete_gasto/";
            $.confirm({
                title: text,
                content: '',
                buttons: {
                  Si: function(){
                    $.post(url,{comprobante_id:comprobante_id}, 
                    function(){
                      $("#"+comprobante_id).remove();
                    });
                  },
                  No: function(){}
                }
            });
        });*/

         $(document).on('click','.com_delete',function(e){
           
            e.preventDefault();
            var comprobante_id = $(this).data('id');
            var url = base_url+"administrador/expensas_delete_gasto/";
            var text = "Desea eliminar el comprobante";
            var text_success = 'Eliminado!';
            
            Swal.fire({
                title: text,
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498DB',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar'

            }).then((result) => {
                if (result.value) {
                    $.post(url,{comprobante_id:comprobante_id}, 
                    function(){
                        Swal.fire(
                              text_success,
                              '',
                              'success'
                        );
                      $("#"+comprobante_id).remove();
                    });
                }
            })

        });
    });
</script>