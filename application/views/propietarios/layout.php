<?php
$rs_explode = explode('_',$this->uri->segment(2));
$methode = '';
$ancla = '';
if(isset($rs_explode[0])){
    $methode = trim($rs_explode[0]);
    $ancla = '#tables-'.$methode;

    if($methode == 'mis'){
        $methode = 'espacios';
        $ancla = '#tables-espacios';
    }    
    if($methode == 'formas'){
        $methode = 'expensas';
        $ancla = '#tables-expensas';
    }
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
        <?php echo $this->my_style->get_css()?>
        <link href="<?=base_url();?>access/css/reservas.css" rel="stylesheet" type="text/css">
        <link href="<?=base_url();?>access/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css">
        <link href="<?=base_url();?>access/css/multi-select.css" rel="stylesheet">
        <link href="<?=base_url();?>access/css/propietario.css" rel="stylesheet">
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
        <SCRIPT TYPE="text/javascript">
            base_url = '<?=base_url()?>';
            var html = 'propietarios';
            controlador = 'propietarios';
            ancla = '<?=$ancla?>'; 
    //alert(ancla);
    $(document).ready(function() {
        $('.hint2basic').summernote();
    });

    </SCRIPT>
    <style type="text/css">
        a.table-button-collapse {
            color: #832b38;
            padding: 5px;
            margin-bottom: 31px;
            background-color: #c7c5c5;
            display: block;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
        }
        .edificio_img{
            max-height: 200px !important;
        }
    </style>
    <style type="text/css">
    .dropdown-menu li {
        display: flex;
    }
</style>
    </head>
    <body>
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="mc-top-menu" role="navigation">
                <div class="navbar-header">
                    <a class="mc-navbar-brand" href="<?=base_url('propietarios');?>">
                        <img src="<?php echo $this->my_style->get_logo()?>" class="logo img-responsive"  width="192" height="40" >
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
                                        <a href="<?=base_url('propietarios/consultas_view/'.$value->id)?>">
                                            <div>
                                                <strong><?=$value->detalle?></strong>
                                                <span class="pull-right text-muted">
                                                    <em><?=date("F j, Y, g:i a",strtotime($value->timestamp))?></em>
                                                </span>
                                            </div>
                                            
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php } ?>

                            </ul>
                        <?php } ?>
                        <!-- /.dropdown-messages -->
                    </li>
                    <!-- /.dropdown -->

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                            <?=(@count($event))? '<span class="badge badge-danger badge-counter">'.count($event).'</span>':'' ?>
                        </a>
                        <?php if(@count($event)) { ?>
                            <ul class="dropdown-menu dropdown-alerts">
                                <?php foreach ($event as $key => $value) { ?>
                                    <li>
                                        <a href="<?=$value['url']?>">
                                            <div>
                                                <i class="fa fa-comment fa-fw"></i> <?=$value['type']?>
                                                <span class="pull-right text-muted small"><?=$value['name']?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <!-- /.dropdown-alerts -->
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                           <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                             <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                          
                        
                            <li><a href="<?=base_url('access/0b70e80da2b1d60041c63225745b0a3c.pdf');?>" target="_blank">
                                <i class="fa fa-user fa-fw"></i>&nbsp;Manual</a>
                            </li>
                  
                            <li>
                                <a href="https://youtu.be/-HK-1_qx2k4" target="_blank" >
                                   <i class="fa fa-video-camera" aria-hidden="true"></i>&nbsp;Reservar espacios </a>
                            </li>                            
                            <li>
                                <a href="https://youtu.be/Vnm_sHW4Cm0" target="_blank">
                                   <i class="fa fa-video-camera" aria-hidden="true"></i>&nbsp;Votar</a>
                            </li>                            
                            <li>
                                <a href="https://youtu.be/VNYlqm2Lbks" target="_blank">
                                   <i class="fa fa-video-camera" aria-hidden="true"></i>&nbsp;Realizar consultas</a>
                                </li>                            
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <!-- /.dropdown -->
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="<?=base_url('propietarios/my_user');?>">
                                <i class="fa fa-user fa-fw"></i>&nbsp;Mi Perfil</a>
                            </li>

                            <li>
                                <a href="<?=base_url();?>auth/logout">
                                    <i class="fa fa-sign-out fa-fw"></i>&nbsp;Salir</a>
                                </li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <!-- /.dropdown -->                    
                        <!-- /.dropdown -->

                    </ul>
                    <!-- /.navbar-top-links --> 
                </nav>
                <!-- sidebar menu -->
                <div id="wrapper-sidebar" role="navigation" id="wrapper-sidebar">
                    <div class="container-fluid" id="navbar-right">

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="thumbnail">

                                    <?php if(is_file(BASEPATH.'../upload/profile/'.$user->photo)){?>
                                        <img src="<?=base_url('upload/profile/'.$user->photo);?>"class="img-thumbnail">
                                    <?php }else{ ?> 
                                        <img src="<?=base_url('upload/edificios/'.$edificio->imagen)?>" alt="<?=$edificio->edificio_nombre;?>" class="edificio_img">  
                                    <?php } ?>
                                    <div class="caption">
                                        <address>
                                            <strong>
                                                <small>
                                                    <?=$user->first_name.' '.$user->last_name?></small>
                                                </strong>
                                                <br>
                                             <img src="https://img.icons8.com/ios-glyphs/15/000000/door-closed.png">&nbspUF <strong><?=$edificio->name?> </strong><br/><i class="fa fa-phone"></i>&nbsp&nbsp<?=$edificio->edificio_telefono?><br>
                                        </address>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $unidades = $this->edificios_model->my_unidad($user->id);
                            if($unidades->num_rows() > 1){ ?>
                                <div class="col-xs-12 ">
                                    <a href="<?=base_url('auth/load_unidades')?>" 
                                        class="table-list-collapse collapsed mis_consorcios">
                                        <?=ucfirst('Mis Unidades')?>
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="col-xs-12">
                                <a href="#tables-expensas" data-toggle="collapse" 
                                class="table-list-collapse collapsed">
                                <?=ucfirst('Expensas')?> <i class="fa fa-credit-card-alt"></i>
                            </a>
                        </div>
                        <div class="col-xs-12">
                            <ul class="mc-table-list collapse <?=($methode == 'expensas')? 'in':''?>" id="tables-expensas">                      
                                <!-- Solo el acceso para 1 consorcios  -->
                                <?php if ($this->edificio_id == 33338) { ?> 
                                <li>
                                    <a href="https://app.consorcioabierto.com/login/administracion/torresdebuenosaires">
                                        <?=ucfirst('consorcio abierto')?>
                                    </a>
                                </li> 
                                <li>
                                    <a href="<?=base_url("propietarios/expensas_list/");?>">
                                        <?=ucfirst('Expensas')?>

                                    </a>
                                </li>  
                                <li>
                                    <a href="<?=base_url("propietarios/pagar/");?>">
                                        <?=ucfirst('Pagar Expensas')?>
                                    </a>
                                </li>            
                                <li>
                                    <a href="<?=base_url("propietarios/expensas_pagos_list/");?>">
                                        <?=ucfirst(' Mis Pagos')?>
                                    </a>
                                </li> 
                                 <?php } else { ?>
                                <li>
                                    <a href="<?=base_url("propietarios/expensas_list/");?>">
                                        <?=ucfirst('Expensas')?>

                                    </a>
                                </li>  
                                <li>
                                    <a href="<?=base_url("propietarios/pagar/");?>">
                                        <?=ucfirst('Pagar Expensas')?>
                                    </a>
                                </li>            
                                <li>
                                    <a href="<?=base_url("propietarios/expensas_pagos_list/");?>">
                                        <?=ucfirst(' Mis Pagos')?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>   
                        <div class="col-xs-12">
                            <a href="#tables-espacios" data-toggle="collapse" 
                            class="table-list-collapse collapsed ">
                            <?=ucfirst('Amenities')?><i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        </a>
                        </div>  
                        <div class="col-xs-12">
                            <ul class="mc-table-list collapse  <?=($methode == 'espacios')? 'in':''?>" id="tables-espacios">
                                <li>
                                    <a href="<?=base_url();?>propietarios/espacios_list">
                                        <?=ucfirst('Listado')?>
                                    </a>
                                </li>                
                                <li>
                                    <a href="<?=base_url();?>propietarios/mis_reservas">
                                        <?=ucfirst('Mis reservas')?>
                                    </a>
                                </li>
                            </ul>
                        </div>   
                        <div class="col-xs-12">
                            <a href="#tables-consultas" data-toggle="collapse" 
                            class="table-list-collapse collapsed">
                            <?=ucfirst('Consultas')?><i class="fa fa-pencil-square" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse <?=($methode == 'consultas')? 'in':''?>"" id="tables-consultas">                      
                            <li>
                                <a href="<?=base_url("propietarios/consultas_create/");?>">
                                    <?=ucfirst('Nueva consulta')?>
                                </a>
                            </li> 

                            <li>
                                <a href="<?=base_url("propietarios/consultas_list/");?>">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li> 

                        </ul>
                    </div>   

                    <div class="col-xs-12">
                        <a href="#tables-propuestas" data-toggle="collapse" 
                            class="table-list-collapse collapsed ">
                            <?=ucfirst('Votaciones')?> <i class="fa fa-check-square-o" aria-hidden="true"></i>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse  <?=($methode == 'propuestas')? 'in':''?>" id="tables-propuestas">
                            <li>
                                <a href="<?=base_url();?>propietarios/propuestas_list">
                                    <?=ucfirst('Listado')?>
                                </a>
                            </li>
                        </ul>
                    </div>

                <div class="col-xs-12">
                    <a href="#tables-circular" data-toggle="collapse" 
                        class="table-list-collapse collapsed ">
                        <?=ucfirst('circular')?> <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    </a>
                </div>  
                <div class="col-xs-12">
                    <ul class="mc-table-list collapse  <?=($methode == 'circular')? 'in':''?>" id="tables-circular">
                        <li>
                            <a href="<?=base_url();?>propietarios/circular_list">
                                <?=ucfirst('Listado')?>
                            </a>
                        </li>
                    </ul>
                </div>    

            <div class="col-xs-12">
                <a href="#tables-asamblea" data-toggle="collapse" 
                class="table-list-collapse collapsed ">
                <?=ucfirst('Asambleas')?> <i class="fa fa-users" aria-hidden="true"></i>
                </a>
            </div>  
            <div class="col-xs-12">
                <ul class="mc-table-list collapse  <?=($methode == 'asamblea')? 'in':''?>" id="tables-asamblea">
                    <li>
                        <a href="<?=base_url();?>propietarios/asamblea_list">
                            <?=ucfirst('Listado')?>
                        </a>
                    </li>
                </ul>
            </div>  


        <div class="col-xs-12">
            <a href="#tables-encargado" data-toggle="collapse" 
            class="table-list-collapse collapsed ">
            <?=ucfirst('personal')?> <i class="fa fa-user" aria-hidden="true"></i>
            </a>
        </div>  
        <div class="col-xs-12">
            <ul class="mc-table-list collapse  <?=($methode == 'encargado')? 'in':''?>" id="tables-encargado">
                <li>
                    <a href="<?=base_url();?>propietarios/encargado_list">
                        <?=ucfirst('Listado')?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-xs-12">
            <a href="#tables-legales" data-toggle="collapse" 
            class="table-list-collapse collapsed ">
            <?=ucfirst('legales')?> <i class="fa fa-file-text-o" aria-hidden="true"></i>
            </a>
        </div>  
        <div class="col-xs-12">
            <ul class="mc-table-list collapse  <?=($methode == 'legales')? 'in':''?>" id="tables-legales">
                <li>
                    <a href="<?=base_url("propietarios/legales_list/".SEGUROS);?>">
                        <?=ucfirst('Seguros')?>
                    </a>
                </li>                            
                <li>
                    <a href="<?=base_url("propietarios/legales_list/".REGLAMENTOS);?>">
                        <?=ucfirst('Reglamentos')?>
                    </a>
                </li>                            
                <li>
                    <a href="<?=base_url("propietarios/legales_list/".CONTRATOS);?>">
                        <?=ucfirst('Contratos')?>
                    </a>
                </li>                            
                <li>
                    <a href="<?=base_url("propietarios/legales_list/".PLANOS);?>">
                        <?=ucfirst('planos')?>
                    </a>
                </li>                            
                <li>
                    <a href="<?=base_url("propietarios/legales_list/".JUCIOS);?>">
                        <?=ucfirst('Jucios')?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-xs-12">
            <a href="#tables-inquilinos" data-toggle="collapse" 
            class="table-list-collapse collapsed">
            <?=ucfirst('inquilinos')?><i class="fa fa-user" aria-hidden="true"></i>
        </a>
        </div>  
        <div class="col-xs-12">
            <ul class="mc-table-list collapse <?=($methode == 'inquilinos')? 'in':''?>"" id="tables-inquilinos">                      
                <li>
                    <a href="<?=base_url();?>propietarios/inquilinos">
                        <?=ucfirst('inquilino')?>
                    </a>
                </li> 

            </ul>
        </div>
       
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

                $(document).ready(function(){

                    //alert(ancla.length);

                    if(ancla.length > 4){
                        $("#wrapper-sidebar").animate({scrollTop: $(ancla).offset().top}, 3000);
                    }
                });

            </script>

    </body>
</html>