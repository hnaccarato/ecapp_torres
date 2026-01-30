<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>
<?php echo form_open("auth/deactivate/".$user->id);?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-sm-7">
			<h2 class="mc-page-header"><?php echo lang('deactivate_heading');?></h2>
			<p><?php echo sprintf(lang('deactivate_subheading'), $user->username);?></p>
		</div>
		<div class="col-xs-12 col-sm-5">
			<ul class="mc-page-actions">
				<li>
					<a type="button" href="<?=base_url();?>admin/"><i class="fa fa-arrow-circle-o-left"></i></a>
				</li>
			</ul>
		</div>
		<div class="col-xs-12">
			<div id="infoMessage"></div>
		</div>
		<div class="col-xs-12">
			<div class="form-group">
				<label><?php echo lang('deactivate_confirm_y_label', 'confirm');?></label>
				<input type="radio" name="confirm" value="yes" checked="checked" />
			</div>
		</div>
		<div class="col-xs-12">
			<div class="form-group">
				<label><?php echo lang('deactivate_confirm_n_label', 'confirm');?></label>
				<input type="radio" name="confirm" value="no" />
			</div>
		</div>
		<?php echo form_hidden($csrf); ?>
		<?php echo form_hidden(array('id'=>$user->id)); ?>		
		<div class="col-xs-12">
			<div class="form-group">
				<?=form_submit('submit', lang('deactivate_submit_btn'), $attrib_submit);?>
			</div>
		</div>
	</div>
</div>
<?=form_close();?>
