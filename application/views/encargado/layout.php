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
    
    <!--/* Firebase Notification*/-->
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.4.1/firebase-messaging.js"></script>
    <script src="<?=base_url();?>access/js/firebase.js"></script>
    <!--/* Fin Firebase Notification*/-->

        <SCRIPT TYPE="text/javascript">
            
            base_url = '<?=base_url()?>';
            var html = 'encargado';
            controlador = 'encargado';
            ancla = '<?=$ancla?>'; 

            $(document).ready(function() {
                $('.hint2basic').summernote();
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
                <a class="mc-navbar-brand" href="<?=base_url();?>encargado">
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
                                    <a href="<?=base_url('encargado/consultas_view/'.$value->id)?>">
                                        <div>
                                            <strong><?=$value->detalle?></strong>
                                            <span class="pull-right text-muted">
                                                <em><?=date("F j, Y, g:i a",strtotime($value->timestamp))?></em>
                                            </span>
                                        </div>
                                        <div><?=substr($value->descripcion,0,20)?>..</div>
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
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=base_url('encargado/my_user');?>"><i class="fa fa-gear fa-plus"></i>&nbsp;Mi Perfil</a></li>
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
                        <h4 class="title_rol">Encargado</h4>
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
                    <a href="#tables-espacios" data-toggle="collapse" 
                        class="table-list-collapse ">
                        <?=ucfirst('espacios')?> <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                    </a>
                </div>  
                <div class="col-xs-12">
                    <ul class="mc-table-list collapse  <?=($methode == 'espacios')? 'in':''?>" id="tables-espacios">
                        <li>
                            <a href="<?=base_url();?>encargado/espacios_list">
                                <?=ucfirst('Listado')?>
                            </a>
                        </li>                            
                        <li>
                            <a href="<?=base_url();?>encargado/espacios_informes">
                                <?=ucfirst('Informes')?>
                            </a>
                        </li>
                    </ul>
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