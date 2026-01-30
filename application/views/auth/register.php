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
	<div class="col-lg-6 d-none d-lg-block bg-login-image" style="padding: 90px">
	    <img src="<?php echo $this->my_style->get_logo_login()?>" class="img-responsive responsive"  width="400" >
	</div>
	<div class="col-lg-6">
		<div class="p-5">
			<div class="text-center">
				<h1 class="h4 text-gray-900 mb-4">
					<?=$edificio->nombre;?><br/>
					<br>
					<small style="display: block; text-align: justify !important;">
						<?=$text?></small>
					</h1>
				</div>
				<hr>
				<?=form_open('',array( 'autocomplete'=>'asdsadasd'));?>
				<section class="mc-login-box">
					<br/>
					<?=$message;?>	
					<div class="col-md-12">
						<div class="form-group">
							<label>Email/Usuario</label>
							<?=form_input($email, '', $attrib);?>
						</div>
					</div>														
					<div class="col-md-12">
						<div class="form-group">
								<label><?=lang('create_user_unidad_label', 'unidad');?></label>
								<select class="form-control" name="unidad" id="unidad_id" required>
									<?php foreach ($unidades->result() as $value) {?>
										<option value="<?=$value->id?>"><?=$value->name?></option>	
									<?php } ?>
								</select>
							</div>
						</div>
					</div>													
					<div class="col-md-12">
						<div class="form-group">
							<label><?=lang('create_user_fname_label', 'first_name');?></label>
							<?=form_input($first_name, '', $attrib);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?=lang('create_user_lname_label', 'last_name');?></label>
							<?=form_input($last_name, '', $attrib);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?=lang('create_user_phone_label', 'phone');?></label>
							<?=form_input($phone, '', $attrib);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?=lang('create_user_password_label', 'password');?></label>
							<?=form_input($password, '', $attrib);?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label><?=lang('create_user_password_confirm_label', 'password_confirm');?></label>
							<?=form_input($password_confirm, '', $attrib);?>
						</div>
					</div>

					<br/>
					<div class="col-md-12" style="text-align: center;">
						<div class="form-group">
							<?=form_submit('submit', lang('create_user_submit_btn'), $attrib_submit);?>
						</div>
					</div>
					<div class="clearfix"></div>
				</section>
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
<script type="text/javascript">

$(document).ready(function(){ 
$("input").attr("autocomplete", "off"); 
$('form').attr('autocomplete', 'off');
});
</script>