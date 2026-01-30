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
                                    <div class="col-lg-6 d-none d-lg-block bg-login-image" style="padding: 30px">
                                        <img src="<?php echo $this->my_style->get_logo_login()?>" class="img-responsive responsive"  width="400" >
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-5">
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">
                                                	<?php echo lang('reset_password_heading');?>
                                                </h1>
                                            </div>
                                            <hr>
                                            <div id="infoMessage"><?php echo $message;?></div>

                                            <?php echo form_open('auth/reset_password/' . $code);?>

                                            	<p>
                                            		<label for="new_password"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label> <br />
                                            		<?php echo form_input($new_password);?>
                                            	</p>

                                            	<p>
                                            		<?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm');?> <br />
                                            		<?php echo form_input($new_password_confirm);?>
                                            	</p>

                                            	<?php echo form_input($user_id);?>
                                            	<?php echo form_hidden($csrf); ?>

                                            	<p><?php echo form_submit('submit','Guardar',array('class'=>'btn btn-primary'));?></p>

                                            <?php echo form_close();?>
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
