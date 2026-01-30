<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<?=form_open("administrador/seguridad_create");?>
<style type="text/css">
	.submit{
		margin-top: 50px;
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?=lang('create_user_heading');?></h2>
			<p><?=lang('create_user_subheading');?></p>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>administrador/seguridad_list/"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
	</div>
	
	<hr class="hr_subrayado">
	
	<div class="row">  
		<div class="col-xs-12">
			<div id="infoMessage"><?=$message;?></div>
		</div>
	
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_fname_label', 'first_name');?></label>
				<?=form_input($first_name, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_lname_label', 'last_name');?></label>
				<?=form_input($last_name, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_email_label', 'email');?></label>
				<?=form_input($email, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_phone_label', 'phone');?></label>
				<?=form_input($phone, '', $attrib);?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_password_label', 'password');?></label>
				<?=form_input($password, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_password_confirm_label', 'password_confirm');?></label>
				<?=form_input($password_confirm, '', $attrib);?>
			</div>
		</div>
	</div>
	<div class="row submit">
		<div class="col-xs-12">
			<div class="form-group">
				<?=form_submit('Guardar', lang('create_user_submit_btn'), $attrib_submit);?>
				
			</div>
		</div>
	</div>
</div>
<?=form_hidden('id')?>
<?=form_close();?>