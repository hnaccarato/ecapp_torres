<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<?=form_open("auth/create_group");?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?=lang('create_group_heading');?></h2>
			<p><?=lang('create_group_subheading');?></p>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>auth/groups"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
		<div class="col-xs-12">
			<div id="infoMessage"><?=$message;?></div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_group_name_label', 'group_name');?></label>
				<?=form_input($group_name, '', $attrib);?>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label><?=lang('create_group_desc_label', 'description');?></label>
				<?=form_input($description, '', $attrib);?>
			</div>
		</div>
		
		<div class="col-xs-12">
			<div class="form-group">
				<?=form_submit('submit', lang('create_group_submit_btn'), $attrib_submit);?>
				
			</div>
		</div>
	</div>
</div>
<?=form_close();?>
