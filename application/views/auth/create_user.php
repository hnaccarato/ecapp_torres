<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<?=form_open("auth/create_user");?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#edificio_id').multiSelect();
	});

</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?=lang('create_user_heading');?></h2>
			<p><?=lang('create_user_subheading');?></p>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>auth/"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
		<hr/>
		<div class="col-xs-12">
			<div id="infoMessage"><?=$message;?></div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_user_edificio_label', 'edificio_id');?></label>
				<select class="form-control" name="edificio_id[]" id="edificio_id" required="required" >
					<?php foreach ($edificios->result() as $value) {?>
						<option value="<?=$value->id?>"><?=$value->nombre?></option>	
					<?php } ?>
				</select>
			</div>
		</div>		
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('group_label', 'group_id');?></label>
				<select class="form-control" name="group_id" required>
						<option value="">Seleccione</option>	
					<?php foreach ($grupos->result() as $value) {?>
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
<div class="copy-fields hide">
  <div class="control-group input-group" style="margin-top:10px">
    <input type="text" name="unidad[]" class="form-control" placeholder="Unidad Funcional">
    <div class="input-group-btn"> 
      <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
    </div>
  </div>
</div>
<script type="text/javascript">

    $(document).ready(function() {

	//here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
      $(".add-more").click(function(){ 
          var html = $(".copy-fields").html();
          $(".after-add-more").after(html);
      });
//here it will remove the current value of the remove button which has been pressed
      $("body").on("click",".remove",function(){ 
          $(this).parents(".control-group").remove();
      });

    });

</script>
<?=form_close();?>
