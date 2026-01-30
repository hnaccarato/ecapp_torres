<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<script type="text/javascript">
$(document).ready(function(){
	/*$(".mc-checkbox").click(function(){
		administrador();
	});
	administrador();*/
	$('#edificio_id').multiSelect();
});

function administrador(){
	$("#edificio_id").removeAttr('multiple');
	

	$("input:checkbox:checked").each(function() {
		rol = $(this).val();
		if(rol == <?=ADMINISTRADOR?>){
			$("#edificio_id").attr('multiple','true');
		}
	 });

}
</script>
<?=form_open(uri_string());?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?=lang('edit_user_heading');?></h2>
			<p><?=lang('edit_user_subheading');?></p>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>auth/"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
		<div class="col-xs-12">
			<div id="infoMessage"><?=$message;?></div>
		</div>

		<div class="col-xs-12 col-md-6">
			<div class="form-group">
				<label><?=lang('create_user_edificio_label', 'edificio_id');?></label>
				<select name="edificio_id[]" class="form-control" id="edificio_id" required="required" multiple="multiple" >
					<option value="">Seleccione</option>
					<?php foreach ($edificios->result() as $value) {?>
						<option 
						value="<?php echo $value->id ?>"
						<?=(in_array($value->id, $my_edificios))? "selected='selected'":" " ?> >
						<?php echo $value->nombre?>
							
						</option>
					<?php } ?>
					
				</select>
			</div>
		</div>		
		<div class="col-xs-12 col-md-6">
			<div class="form-group">
				<label><?=lang('edit_user_fname_label', 'first_name');?></label>
				<?=form_input($first_name, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('edit_user_lname_label', 'last_name');?></label>
				<?=form_input($last_name, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('edit_user_phone_label', 'phone');?></label>
				<?=form_input($phone, '', $attrib);?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('edit_user_password_label', 'password');?></label>
				<?=form_input($password, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('edit_user_password_confirm_label', 'password_confirm');?></label>
				<?=form_input($password_confirm, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-12">
			<?php if ($this->ion_auth->is_admin()): ?>
					<h3><?=lang('edit_user_groups_heading');?></h3>
					<?php foreach ($groups as $group):?>
						
							<label class="checkbox">
								<?php
									$gID=$group['id'];
									$checked = null;
									$item = null;
									foreach($currentGroups as $grp) {
										if ($gID == $grp->id) {
											$checked= ' checked="checked"';
											break;
										}
									}
								?>
								<input type="checkbox" class="mc-checkbox" name="groups[]" value="<?=$group['id'];?>"<?=$checked;?>>
								<?=htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
							</label>
					
					<?php endforeach ?>
			<?php endif ?>
		</div>
		<div class="col-xs-12">
			<div class="form-group">
				<?=form_submit('submit', lang('edit_user_submit_btn'), $attrib_submit);?>
				
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
<?=form_hidden('id', $user->id);?>
<?=form_hidden($csrf); ?>
<?=form_close();?>
