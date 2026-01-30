
<?php

$rs_explode = explode('_',$this->uri->segment(2));
$methode = '';
$ancla =0;
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
    <title>Building Apps</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url();?>access/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url();?>access/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="<?=base_url();?>access/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url();?>access/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?=base_url();?>access/css/multi-select.css" rel="stylesheet">
    <link href="<?=base_url();?>access/css/jquery-confirm.css" rel="stylesheet">
    <!-- Morris Charts CSS -->

    <!-- Custom Fonts -->
    <link href="<?=base_url();?>access/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Override -->
    <link href="<?=base_url();?>access/css/style.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">

    <script src="<?=base_url();?>access/js/jquery-1.11.1.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url();?>access/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=base_url();?>access/vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Morris Charts JavaScript -->

    <!-- Custom Theme JavaScript -->
    <script src="<?=base_url();?>access/dist/js/sb-admin-2.js"></script>
    <script src="<?=base_url();?>access/js/bootstrap-datepicker.min.js"></script>
    <script src="<?=base_url();?>access/js/make-crude-admin.js"></script>
    <script src="<?=base_url();?>access/js/jquery.multi-select.js"></script>
    <script src="<?=base_url();?>access/js/jquery-confirm.min.js"></script>
    <script src="<?=base_url();?>access/DataTables/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>

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
</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="mc-top-menu" role="navigation">
            <div class="navbar-header">
                <a class="mc-navbar-brand" href="<?=base_url();?>admin">
                    <img src="<?=base_url('access/images/logo.png')?>" class="img-responsive" height="50">
                </a>
            </div>
            <!-- /.navbar-header -->
            <a href="javascript:void(0)" class="mc-sidebar-toggle" id="mc-sidebar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <i class="fa fa-navicon"></i>
            </a>
            <ul class="nav navbar-top-links navbar-right hidden-xs">
  
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=base_url();?>auth/create_user"><i class="fa fa-gear fa-plus"></i>&nbsp;Crear</a></li>
                        <li><a href="<?=base_url();?>auth"><i class="fa fa-gear fa-table"></i>&nbsp;Listar</a></li>
                        <li class="divider"></li>
                        <li><a href="<?=base_url();?>auth/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links --> 
        </nav>
        <!-- sidebar menu -->
        <div id="wrapper-sidebar" role="navigation" id="wrapper-sidebar">
            <div class="container-fluid" id="navbar-right">

                <div class="row">
                    <div class="card">
                        <div class="container">
                            <h4><b><?=$user->first_name.' '.$user->last_name?></b></h4> 
                            <p><?php echo $user->email ?></p> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <a href="#tables-list3" data-toggle="collapse" 
                            class="table-list-collapse collapsed" aria-expanded="false">
                            <?=ucfirst('Empresa')?> <i class="fa fa-cubes"></i></a>
                        </a>
                    </div>  
                    <div class="col-xs-12">
                        <ul class="mc-table-list collapse" id="tables-list3">
                            <li>
                                <a href="<?=base_url();?>admin/empresa__create">
                                    <?=ucfirst('Nueva Empresas')?>
                                </a>
                            </li>
                            <li>
                                <a href="<?=base_url();?>admin/empresa__list">
                                    <?=ucfirst('Empresas');?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <a href="#tables-list1" data-toggle="collapse" 
                        class="table-list-collapse collapsed" aria-expanded="false">
                        <?=ucfirst('Usuarios')?> <i class="fa fa-user"></i>
                    </a>
                </div>  
                <div class="col-xs-12">
                    <ul class="mc-table-list collapse" id="tables-list1">
                        <li>
                            <a href="<?=base_url();?>auth/create_user">
                                <?=ucfirst('Nuevo Usuario')?>
                            </a>
                        </li>                      
                        <li>
                            <a href="<?=base_url();?>auth">
                                <?=ucfirst('Listado')?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>          
            <div class="row">
                <div class="col-xs-12">
                    <a href="#tables-list2" data-toggle="collapse" 
                    class="table-list-collapse collapsed" aria-expanded="false">
                    <?=ucfirst('Administracion')?> <i class="fa fa-building"></i>
                </a>
            </div>  
            <div class="col-xs-12">
                <ul class="mc-table-list collapse" id="tables-list2">
                    <li>
                        <a href="<?=base_url();?>admin/edificios_create">
                            <?=ucfirst('Nuevo Edificio')?>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>admin/edificios_list">
                            <?=ucfirst('Listado');?>
                        </a>
                    </li>
                </ul>
            </div>
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

  /*  $(document).ready(function(){
        if(ancla.length >6){
            $("#wrapper-sidebar").animate({scrollTop: $(ancla).offset().top}, 3000);
        }
    });*/

</script>

</body>
</html>