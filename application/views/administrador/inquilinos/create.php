
<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<?=form_open('administrador/inquilinos_create/'.$user_id);?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?=lang('create_user_heading');?></h2>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>administrador/inquilinos/<?=$user_id?>"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
		<div class="col-xs-12">
		    <hr class="hr_subrayado">
		</div>
	</div>
	<div class="panel panel-default">
	  <div class="panel-body">
	  	<p><?=lang('create_user_subheading');?></p>
	   <div class="row">
	   		<div class="col-xs-12">
	   			<div id="infoMessage"><?=$message;?></div>
	   		</div>
	   		<div class="col-xs-6">
	   			<div class="form-group">
	   				<label>Unidades</label>
	   				<select name="unidad_id" class="form-control" id="unidad_id" required="required">
	   					<?php foreach ($unidades->result() as $value) {?>
	   						<option value="<?=$value->id?>"><?=$value->name?></option>
	   					<?php } ?>
	   				</select>
	   			</div>
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
	   		<div class="col-xs-12">
	   			<div class="form-group">
	   				<?=form_submit('submit', lang('create_user_submit_btn'), $attrib_submit);?>
	   				
	   			</div>
	   		</div>
	   	</div>
	   </div>
	  </div>
	</div>

	
<?=form_close();?>
<script type="text/javascript">
    $(document).ready(function (){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

    })
</script>