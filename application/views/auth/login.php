<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

   <title><?php echo $this->config->item('site_name'); ?></title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?=base_url();?>access/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url();?>access/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Override -->
    <link href="<?=base_url();?>access/dist/css/sb-admin-2.css" rel="stylesheet" type="text/css">
    <?php $attrib = array('class' => 'form-control');?>
    <?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
    <style type="text/css">
        body {
            background-color: <?=$this->my_style->get_color()?> !important;
        }
        #infoMessage{
            color: red;
        }
        #footer {
            color: aliceblue;
            text-align: center;
        }
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-8 col-lg-12 col-md-8 col-sm-6" >

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <div class="panel panel-default" style="margin-top: 40px; border-radius: 72px;">
                                <div class="panel-body">
                                    <div class="col-lg-6 d-none d-lg-block bg-login-image" style="padding: 70px">
                                        <img src="<?php echo $this->my_style->get_logo_login()?>" class="img-responsive responsive"  width="350" >
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-5">
                                            <hr>
                                            <?=form_open("auth/login");?>
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h2><?=lang('login_heading');?></h2>
                                                        <p><?=lang('login_subheading');?></p>
                                                        <div id="infoMessage"><?=$message;?></div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <label><?=lang('login_identity_label', 'identity');?></label>
                                                            <?=form_input($identity, '', $attrib);?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <label><?=lang('login_password_label', 'password');?></label>
                                                            <?=form_input($password, '', $attrib);?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <label><?=lang('login_remember_label', 'remember');?></label>
                                                            <?=form_checkbox('remember', '1', FALSE, 'id="remember"');?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <?=form_submit('submit', lang('login_submit_btn'), $attrib_submit);?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <p><a href="forgot_password"><?=lang('login_forgot_password');?></a></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?=form_close();?>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
<footer>
    <div class="col-md-12">
        <div class="col-md-12">
            <p id="footer">
                Torres de Buenos Aires © 2017 | RPA 13038 | Alicia M. de Justo N° 1780 1H - Puerto Madero, CABA, Argentina | Telefono: +5411 4312-4062/4987
            </p>
        </div>
    </div>
</footer>
</body>

</html>
