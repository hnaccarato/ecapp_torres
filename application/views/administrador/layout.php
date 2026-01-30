<?php

    $rs_explode = explode('_',$this->uri->segment(2));
    $methode = '';
    $ancla = '';
    if(isset($rs_explode[0])){
        $methode = trim($rs_explode[0]);
        $ancla = '#tables-'.$methode;
    }
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $this->config->item('site_name'); ?></title>
    <html lang="en">
    <meta charset="utf-8">
    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url();?>access/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url();?>access/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="<?=base_url();?>access/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url();?>access/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="<?=base_url();?>access/vendor/morrisjs/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?=base_url();?>access/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Override -->
    <!--
    <link href="<?=base_url();?>access/css/style.css" rel="stylesheet" type="text/css">
     -->
    <?php echo $this->my_style->get_css() ?>
    <link href="<?=base_url();?>access/css/reservas.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>access/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>access/css/multi-select.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <!-- javascripts-->
    
    <script src="<?=base_url();?>access/js/jquery-1.11.1.min.js"></script>
    <script src="<?=base_url();?>access/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="<?=base_url();?>access/js/reservas.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url();?>access/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=base_url();?>access/vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Morris Charts JavaScript -->
    <script src="<?=base_url();?>access/vendor/raphael/raphael.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?=base_url();?>access/dist/js/sb-admin-2.js"></script>
    <script src="<?=base_url();?>access/js/bootstrap-datepicker.min.js"></script>
    <script src="<?=base_url();?>access/js/make-crude-admin.js"></script>
    <script src="<?=base_url();?>access/js/jquery.multi-select.js"></script>
    
    <script src="<?=base_url();?>access/sweetalert2/dist/sweetalert2.all.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
    <link href="<?=base_url();?>access/css/monthly.css" rel="stylesheet" type="text/css">
    <script src="<?=base_url();?>access/js/monthly.js"></script>
    <script src="<?=base_url();?>access/js/jquery.quicksearch.js"></script>
    
    <!--/* Firebase Notification*/-->
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase-messaging.js"></script>
    <script src="<?=base_url();?>access/js/firebase.js"></script>
    <!--/* Fin Firebase Notification*/-->

        <SCRIPT TYPE="text/javascript">
            
            base_url = '<?=base_url()?>';
            var html = 'administrador';
            controlador = 'administrador';
            ancla = '<?=$ancla?>'; 

            $(document).ready(function() {
                // $('.hint2basic').summernote();
                var $editor = $('.hint2basic');

                $editor.summernote({
                    onpaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');

                        e.preventDefault();

                        document.execCommand('insertText', false, bufferText);
                    }
                });
            });

        </SCRIPT>

</head>
<style type="text/css">
    .dropdown-menu li {
        display: flex;
    }
</style>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="mc-top-menu" role="navigation">
            <div class="navbar-header">
                <a class="mc-navbar-brand" href="<?=base_url();?>administrador">
                    <img src="<?php echo $this->my_style->get_logo()?>"  width="192" height="40" class="logo img-responsive" data-pin-nopin="true">
                </a>
            </div>
            <!-- /.navbar-header -->
            <a href="javascript:void(0)" class="mc-sidebar-toggle" id="mc-sidebar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <i class="fa fa-navicon"></i>
            </a>
            <ul class="nav navbar-top-links navbar-right hidden-xs">
                 <li class="dropdown">
                     <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                         <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
                         <?=($messenges->num_rows())? '<span class="badge badge-danger badge-counter">'.$messenges->num_rows().'</span>':'' ?>
                     </a>
                 
                     <?php if($messenges->num_rows()){ ?>
                         <ul class="dropdown-menu dropdown-messages">
                             <?php foreach ($messenges->result() as $value) { ?>
                                 <li>
                                    <a href="<?=base_url('administrador/consultas_view/'.$value->id)?>">
                                        <div>
                                            <strong><?=$value->detalle?></strong>
                                            <span class="pull-right text-muted">
                                                <em><?=date("F j, Y, g:i a",strtotime($value->timestamp))?></em>
                                            </span>
                                        </div>
                                        <div><hr></div>
                                    </a>
                                 </li>
                                 <li class="divider"></li>
                            <?php } ?>

                        </ul>
                    <?php } ?>
                    <!-- /.dropdown-messages -->
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                       <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                         <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=base_url('access/9ee5d288299a1b4b5a92555cf0347d5d.pdf');?>" target="_blank">
                            <i class="fa fa-user fa-fw"></i>&nbsp;Manual Administrador</a>
                        </li>
                        <li><a href="<?=base_url('access/0b70e80da2b1d60041c63225745b0a3c.pdf');?>" target="_blank">
                            <i class="fa fa-user fa-fw"></i>&nbsp;Manual Propietarios</a>
                        </li>

                        <li>
                            <a href="https://youtu.be/WHRJ215x9Vw" target="_blank" >
                               <i class="fa fa-video-camera" aria-hidden="true"></i>&nbsp;Crear espacios</a>
                            </li>                            
                            <li>
                            <a href="https://youtu.be/zHBtp2E08Co" target="_blank">
                               <i class="fa fa-video-camera" aria-hidden="true"></i>&nbsp;Cargar expensas</a>
                            </li>                                                   
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=base_url('administrador/my_user');?>"><i class="fa fa-gear fa-plus"></i>&nbsp;Mi Perfil</a></li>
                        <li class="divider"></li>
                        <li><a href="<?=base_url();?>auth/logout"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links --> 
        </nav>
        <!-- sidebar menu -->
        <div id="wrapper-sidebar" role="navigation" >
            <div class="container-fluid"> 
                <div class="row">
                  <div class="col-xs-12">

                    <div class="thumbnail">
                        <h4 class="title_rol">Administrador</h4>
                        <hr>
                        <?php if(is_file(BASEPATH.'../upload/profile/'.$user->photo)){?>
                            <img src="<?=base_url('upload/profile/'.$user->photo);?>"class="img-thumbnail">
                        <?php }else{ ?> 
                            <img src="<?=base_url('upload/edificios/'.$edificio->imagen)?>" alt="<?=$edificio->nombre;?>" class="edificio_img">  
                        <?php } ?>
                        <div class="caption">
                            <address>
                                <strong><?=$edificio->nombre?></strong><br>
                                <?=$edificio->direccion?><br>
                            </address>
                            <address>             
                                <strong><?=$user->first_name.' '.$user->last_name?></strong><br>
                            </address>
                        </div>
                    </div>
                  </div>
                </div> 
                <div class="row">
                   <?php
                     $edificio = $this->edificios_model->my_edificios($user->id);
                     if($edificio->num_rows() > 1){ ?>
                       <div class="col-xs-12">
                           <a href="<?=base_url('auth/load_edificio')?>" 
                           class="table-list-collapse collapsed mis_consorcios">
                           <?=ucfirst('Mis Consorcios')?>
                         </a>
                       </div>
                     <?php } ?>

                    <div class="col-xs-12">
                        <a href="<?=base_url("administrador/edificios_update/");?>" class="table-list-collapse collapsed">
                            <?=ucfirst('Mi Consorcio')?>  <i class="fa fa-building-o"></i>
                        </a>
                    </div>  
                   
                    <div class="col-xs-12">
                        <a href="#tables-expensas" data-toggle="collapse" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Expensas')?> <i class="fa fa-credit-card-alt"></i>
                    </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'expensas')? 'in':''?>" id="tables-expensas">                 
                           
                            <li>
                                <a href="<?=base_url("administrador/expensas_create/");?>">
                                  Crear Expensa
                                </a>
                            </li>                             
                            <li>
                                <a href="<?=base_url("administrador/expensas_list/");?>">
                                  Listado de Expensas
                                </a>
                            </li> 
                           <li>
                               <a href="<?=base_url("administrador/expensas_pagos_list/");?>">
                                   Listado de pagos
                               </a>
                           </li
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-consultas" data-toggle="collapse" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Consultas')?> <i class="fa fa-pencil-square-o"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse <?=($methode == 'consultas')? 'in':''?>" id="tables-consultas">
                            <li>
                                <a href="<?=base_url();?>administrador/consultas_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>  

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-espacios" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('espacios')?> <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'espacios')? 'in':''?>" id="tables-espacios">
                            <li>
                                <a href="<?=base_url();?>administrador/espacios_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/espacios_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url();?>administrador/espacios_informes">
                                    <?=ucfirst('Informes')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-circular" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('circular')?> <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'circular')? 'in':''?>" id="tables-circular">
                            <li>
                                <a href="<?=base_url();?>administrador/circular_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/circular_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-asamblea" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('Asambleas')?><i class="fa fa-users" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'asamblea')? 'in':''?>" id="tables-asamblea">
                            <li>
                                <a href="<?=base_url();?>administrador/asamblea_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/asamblea_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-propuestas" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('Votaciones')?> <i class="fa fa-check-square-o" aria-hidden="true"></i>

                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'propuestas')? 'in':''?>" id="tables-propuestas">
                            <li>
                                <a href="<?=base_url();?>administrador/propuestas_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/propuestas_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-encargado" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('personal')?><i class="fa fa-user" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'encargado')? 'in':''?>" id="tables-encargado">
                            <li>
                                <a href="<?=base_url();?>administrador/encargado_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/encargado_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/legales_list/7">
                                    <?=ucfirst('Organigrama')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>  

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-legales" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('legales')?> <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'legales')? 'in':''?>" id="tables-legales">
                            <li>
                                <a href="<?=base_url("administrador/legales_list/".SEGUROS);?>">
                                    <?=ucfirst('Seguros')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url("administrador/legales_list/".REGLAMENTOS);?>">
                                    <?=ucfirst('Reglamentos')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url("administrador/legales_list/".CONTRATOS);?>">
                                    <?=ucfirst('Contratos')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url("administrador/legales_list/".PLANOS);?>">
                                    <?=ucfirst('planos')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url("administrador/legales_list/".JUCIOS);?>">
                                    <?=ucfirst('Juicios')?>
                                </a>
                            </li>                            
                            <li>
                                <a href="<?=base_url("administrador/legales_list/6");?>">
                                    <?=ucfirst('Inventarios')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> 

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-unidad" data-toggle="collapse" class="table-list-collapse collapsed">
                            <?=ucfirst('unidades')?>  <i class="fa fa-group"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'unidad')? 'in':''?>" id="tables-unidad">
                            <li>
                                <a href="<?=base_url();?>administrador/unidad_create"> 
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/unidad_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-propietarios" data-toggle="collapse" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Propietarios')?> <i class="fa fa-user"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'propietarios')? 'in':''?>" id="tables-propietarios">
                            <li>
                                <a href="<?=base_url();?>administrador/propietarios_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/propietarios_list">
                                    <?=ucfirst('listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>  
                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-cuenta" data-toggle="collapse" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Cuenta Corrientes')?> <i class="fa fa-newspaper-o"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'propietarios')? 'in':''?>" id="tables-cuenta">
                            <li>
                                <a href="<?=base_url();?>administrador/cuenta_corriente_list">
                                    <?=ucfirst('listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>  

                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-seguridad" data-toggle="collapse" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Seguridad')?> <i class="fa fa-user"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'seguridad')? 'in':''?>" id="tables-seguridad">
                            <li>
                                <a href="<?=base_url();?>administrador/seguridad_create/">
                                    <?=ucfirst('Crear')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>administrador/seguridad_list">
                                    <?=ucfirst('listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>                  

                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?=base_url();?>auth/logout" 
                        class="table-list-collapse collapsed">
                        <?=ucfirst('Salir del sistema')?> <i class="fa fa-sign-out" aria-hidden="true"></i>
                        </a>
                    </div>  
                </div>  

            </div>
        </div>
<!-- /.navbar-static-side -->
<!-- /#wrapper -->
<div id="wrapper-page">
    <?=$content_for_layout;?>
</div>
<!-- /#page-wrapper -->
</div>
<script>
$('#mc-sidebar-toggle').click(function(){
    $('#wrapper-sidebar').toggleClass('closed');
});


 $(document).on('click','.delete',function(e){
   
    e.preventDefault();
    var id = $(this).data('primary-key');
    var url = $(this).attr('href');
    var text = 'Desea eliminar '+elemento+'?';
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

            $.get(url,function(){
                
                Swal.fire(
                      text_success,
                      '',
                      'success'
                );
                   
                $("#"+id).remove();
            });
        }
    })

});
 
$(document).ready(function(){
    if(ancla.length > 4){
       $("#wrapper-sidebar").animate({scrollTop: $(ancla).offset().top}, 3000);
    }
    
    $(document).on('mouseenter','.delete',function(e){
        $('.delete').attr('title', 'Eliminar '+elemento);
    });     

    $(document).on('mouseenter','a .fa-pencil',function(e){
        $(this).children("a").attr('title','Editar '+elemento);
    }); 

}); 

</script>
<!-- The core Firebase JS SDK is always required and must be listed first -->

</body>
</html>