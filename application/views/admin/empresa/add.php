
<div class="row">
    <div class="col-md-12">
      	<div class="box box-info">
            <div class="box-header with-border">
              	<h3 class="box-title">Empresa Add</h3>
            </div>
            <?php echo form_open_multipart('admin/empresa__create'); ?>
          	<div class="box-body">
          		<div class="row clearfix">
					<div class="col-md-6">
						<label for="nombre" class="control-label"><span class="text-danger">*</span>Nombre</label>
						<div class="form-group">
							<input type="text" name="nombre" value="<?php echo $this->input->post('nombre'); ?>" class="form-control" id="nombre" />
							<span class="text-danger"><?php echo form_error('nombre');?></span>
						</div>
					</div>
					<div class="col-md-6">
						<label for="color" class="control-label"><span class="text-danger">*</span>Color</label>
						<div class="form-group">
							<input type="color" name="color" value="<?php echo $this->input->post('color'); ?>" class="form-control" id="color" />
							<span class="text-danger"><?php echo form_error('color');?></span>
						</div>
					</div>						
					<div class="col-md-6">
						<label for="color_icono" class="control-label"><span class="text-danger">*</span>Color Iconos</label>
						<div class="form-group">
							<input type="color" name="color_icono" value="<?php echo $this->input->post('color_icono'); ?>" class="form-control" id="color_icono" />
							<span class="text-danger"><?php echo form_error('color_icono');?></span>
						</div>
					</div>					
					<div class="col-md-6">
						<label for="url" class="control-label"><span class="text-danger">*</span>URL</label>
						<div class="form-group">
							<input type="url" name="url" value="<?php echo $this->input->post('url'); ?>" class="form-control" id="url" />
							<span class="text-danger"><?php echo form_error('url');?></span>
						</div>
					</div>
				</div>
				<div class="row clearfix">
					<div class="col-md-6">
						<label for="logo" class="control-label">Logo</label>
						<div class="form-group">
							<input type="file" name="logo" value="<?php echo $this->input->post('logo'); ?>" class="form-control" id="logo" />
						</div>
					</div>
					<div class="col-md-6">
						<label for="logo_login" class="control-label">Logo Login</label>
						<div class="form-group">
							<input type="file" name="logo_login" value="<?php echo $this->input->post('logo_login'); ?>" class="form-control" id="logo_login" />
						</div>
					</div>
				</div>
			</div>
          	<div class="box-footer">
            	<button type="submit" class="btn btn-success">
            		<i class="fa fa-check"></i> Guardar
            	</button>
          	</div>
            <?php echo form_close(); ?>
      	</div>
    </div>
</div>