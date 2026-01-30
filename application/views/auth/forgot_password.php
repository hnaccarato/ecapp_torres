<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Building Apps</title>

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

    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <div class="panel panel-default" style="margin-top: 40px;">
                                <div class="panel-body">
                                    <div class="col-lg-6 d-none d-lg-block bg-login-image" style="padding: 90px">
                                        <img src="<?php echo $this->my_style->get_logo_login()?>" class="img-responsive responsive"  width="400" >
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-5">
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">Bienvenido a Building Apps</h1>
                                            </div>
                                            <hr>
                                            <?php echo form_open("auth/forgot_password");?>
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h2><?php echo lang('forgot_password_heading');?></h2>
                                                        <p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
                                                        <div id="infoMessage"><?=$message;?></div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label>
                                                            <?=form_input($identity, '', $attrib);?>
                                                        </div>
                                                    </div>              
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <?=form_submit('submit', lang('forgot_password_submit_btn'), $attrib_submit);?>
                                                        </div>
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

</body>

</html>
