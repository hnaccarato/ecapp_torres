
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>ECAPP - Torres De Buenos Aires</title>
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
    <link href="<?=base_url();?>access/css/reservas.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url();?>access/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css">
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
  
        <SCRIPT TYPE="text/javascript">
            base_url = '<?=base_url()?>';
        </SCRIPT>
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
</style>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="mc-top-menu" role="navigation">
            <div class="navbar-header">
                <a class="mc-navbar-brand" href="<?=base_url();?>">
                    <img src="<?php echo $this->my_style->get_logo()?>" class="img-responsive" height="40">
                </a>
            </div>
            <ul class="nav navbar-top-links navbar-right hidden-xs">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="<?=base_url();?>auth/logout">
                                <i class="fa fa-sign-out fa-fw"></i> Logout
                            </a>
                        </li>
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
                        <?php foreach ($groups->result() as $value) {?>
                        <div class="col-sm-6 col-md-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong  class="panel-title">
                                        <?=$value->name?>
                                    </strong>
                                </div>
                                <div class="panel-body">
                                    <div class="thumbnail">
                                        <img src="<?=base_url('access/images/default.jpg')?>" alt="fotos">
                                        <div class="caption">
                                            <p>
                                                <a href="<?=base_url($value->description)?>" class="acces btn btn-primary" role="button" 
                                                data-id="">Acceder</a> 
                                            </p>
                                        </div>
                                    </div> 

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
        $.post('<?=base_url()?>auth/load_unidades',{id:id},function(data){
            window.location.replace("<?=base_url($url)?>");

        })
    });
</script>