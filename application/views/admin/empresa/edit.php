<div class="row">
    <div class="col-md-12">
      	<div class="box box-info">
            <div class="box-header with-border">
              	<h3 class="box-title">Empresa Edit</h3>
            </div>
			<?php echo form_open_multipart('admin/empresa_update/'.$empresa['id']); ?>
			<div class="box-body">
				<div class="row clearfix">
					<div class="col-md-6">
						<label for="nombre" class="control-label"><span class="text-danger">*</span>Nombre</label>
						<div class="form-group">
							<input type="text" name="nombre" value="<?php echo ($this->input->post('nombre') ? $this->input->post('nombre') : $empresa['nombre']); ?>" class="form-control" id="nombre" />
							<span class="text-danger"><?php echo form_error('nombre');?></span>
						</div>
					</div>
					<div class="col-md-6">
						<label for="color" class="control-label"><span class="text-danger">*</span>Color</label>
						<div class="form-group">
							<input type="color" name="color" value="<?php echo ($this->input->post('color') ? $this->input->post('color') : $empresa['color']); ?>" class="form-control" id="color" />
							<span class="text-danger"><?php echo form_error('color');?></span>
						</div>
					</div>					
					<div class="col-md-6">
						<label for="color_icono" class="control-label"><span class="text-danger">*</span>color_icono</label>
						<div class="form-group">
							<input type="color" name="color_icono" value="<?php echo ($this->input->post('color_icono') ? $this->input->post('color_icono') : $empresa['color_icono']); ?>" class="form-control" id="color_icono" />
							<span class="text-danger"><?php echo form_error('color_icono');?></span>
						</div>
					</div>					
					<div class="col-md-6">
						<label for="url" class="control-label"><span class="text-danger">*</span>url</label>
						<div class="form-group">
							<input type="url" name="url" value="<?php echo ($this->input->post('url') ? $this->input->post('url') : $empresa['url']); ?>" class="form-control" id="url" />
							<span class="text-danger"><?php echo form_error('url');?></span>
						</div>
					</div>
				</div>
				<div class="row clearfix">
					<div class="col-md-6">

						<div class="thumbnail">
					      <a href="<?=base_url('upload/empresa/logo/'.$empresa['logo'])?>">
					        <img src="<?=base_url('upload/empresa/logo/'.$empresa['logo'])?>" alt="Lights" style="width:30%">
					        <div class="caption">
					          <label for="logo" class="control-label">Logo</label>
					          <div class="form-group">
					          	<input type="file" name="logo" class="form-control" id="logo" />
					          </div>
					        </div>
					      </a>
					    </div>
					</div>
					<div class="col-md-6">
							<div class="thumbnail">
						      <a href="<?=base_url('upload/empresa/logo/'.$empresa['logo_login'])?>">
						        <img src="<?=base_url('upload/empresa/logo/'.$empresa['logo_login'])?>" alt="Lights" style="width:30%">
						        <div class="caption">
						        	<label for="logo_login" class="control-label">Logo Login</label>
						        	<div class="form-group">
						        		<input type="file" name="logo_login"  class="form-control" id="logo_login" />
						        	</div>
						        </div>
						      </a>
						    </div>

					</div>
				</div>
			</div>
			<div class="box-footer">
            	<button type="submit" class="btn btn-success">
					<i class="fa fa-check"></i> Save
				</button>
	        </div>				
			<?php echo form_close(); ?>
		</div>
    </div>
</div