<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=ucfirst($this->my_style->get_name())?>s</title>
    <html lang="en">
    <head>
          <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
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
    <?php echo $this->my_style->get_css() ?>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
     <!-- jQuery -->
    <script src="<?=base_url();?>access/vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url();?>access/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=base_url();?>access/vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Morris Charts JavaScript -->
    <script src="<?=base_url();?>access/vendor/raphael/raphael.min.js"></script>
    <script src="<?=base_url();?>access/vendor/morrisjs/morris.min.js"></script>
    <script src="<?=base_url();?>access/data/morris-data.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="<?=base_url();?>access/dist/js/sb-admin-2.js"></script>
    <script src="<?=base_url();?>access/js/bootstrap-datepicker.min.js"></script>
    <script src="<?=base_url();?>access/js/make-crude-admin.js"></script>
        <SCRIPT TYPE="text/javascript">
            base_url = '<?=base_url()?>';
        </SCRIPT>
    </head>
</head>
</head>
<style type="text/css">
    .glyphicon-ok{
      color:green;
  }
  .glyphicon-remove{
      color:red;
  }
  .container{
    margin-top: 80px;
}
.img_build{
    height: 250px !important;
}

.thumbnail{
    height: 456px !important;
}

</style>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="mc-top-menu" role="navigation">
            <div class="navbar-header">
                <a class="mc-navbar-brand" href="<?=base_url()?>" >
                    <img src="<?php echo $this->my_style->get_logo()?>"  width="192" height="40" class="img-responsive" data-pin-nopin="true">
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
                        <li><a href="<?=base_url();?>auth/logout"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links --> 
        </nav>
        <div class="container">
            <div class="row" >
                <div class="col-md-12">
                    <div class="row">
                    <?php foreach ($edificios->result() as $value) {?>
                       <div class="col-sm-6 col-md-4">
                         <div class="thumbnail"> 
                            <?php if(is_file(BASEPATH.'../upload/edificios/'.$value->imagen)){?>
                                 <img src="<?=base_url('upload/edificios/'.$value->imagen)?>" 
                                 alt="fotos" class="img_build">
                            <?php }else{?>
                                <img src="<?=base_url('access/images/default.jpg')?>" alt="fotos" class="img_build">
                            <?php } ?>
                           
                           <div class="caption">
                             <h3><?=$value->nombre?></h3>
                             <p><?=$value->direccion?></p>
                             <p><?=$value->telefono?></p>
                             <p>
                             <a href="javascript:void(0)" class="acces btn btn-primary" role="button" 
                             data-id="<?=$value->id?>">Acceder</a> 
                             </p>
                           </div>
                         </div>
                       </div>
                    <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<script type="text/javascript">
    $(".acces").click(function(){
        var id = $(this).data('id');
        $.post('<?=base_url()?>auth/load_edificio',{id:id},function(data){
           window.location.replace("<?=base_url($url)?>");

        })
    });
</script>