<div class="row">
	<div class="col-xs-12 col-sm-7">
	    <h3 class="mc-page-header">Consulta</h3>
	</div>
	<div class="col-xs-12 col-sm-5">
	    <ul class="mc-page-actions">
	        <li>
	            <a type="button" href="<?=base_url();?>inquilinos/consultas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
	        </li>
	    </ul>
	</div>
</div>

<hr>
<div class="row">
	
	<div class="col-sm-12">
		<div class="container">
			<ul class="timeline">	
				<li>
					<div class="timeline-badge"><i class="glyphicon glyphicon-check"></i></div>
					<div class="timeline-panel">
						<div class="timeline-heading">
							<h4><strong><?=ucfirst($consulta->detalle)?></strong></h4>
							<hr>
							<h4 class="timeline-title">
								<?=ucfirst($consulta->nombre." ".$consulta->apellido)?>  
								<small class="pull-right"><?=$consulta->unidad?> - <?=$consulta->departamento?><br></small>
							</h4>
					
							<p>
								<small class="text-muted"><i class="glyphicon glyphicon-time"></i> 
									<?=date("d/m/Y H:i",strtotime($consulta->timestamp))?>
								</small>
							</p>
						</div>
						<div class="timeline-body">
							<p><?=ucfirst($consulta->descripcion)?></p>
							<?php if(!empty($consulta->file)){ ?>
								<hr>
								<div class="btn-group">
									<a class="btn btn-primary" href="<?=base_url('/upload/consultas/'.$consulta->file)?>" target="_blank" role="button">Descargar</a>
								</div>
							<?php } ?>
						</div>
					</div>
				</li>

				<?php foreach ($respuestas->result() as $respuesta) {?>
					<li class="<?=($respuesta->is_admin)? 'timeline-inverted':'' ?>">
						<div class="timeline-badge">
							<i class="glyphicon">
								<img alt="64x64" class="media-object img-circle" src="<?=base_url(($respuesta->is_admin)? '/access/images/adm.png':'/access/images/user.png')?>" data-holder-rendered="true" style="width: 64px; height: 64px;">
							</i>
						</div>
						<div class="timeline-panel">
							<div class="timeline-heading">
								<h4 class="timeline-title">
									<?=ucfirst($respuesta->nombre." ".$respuesta->apellido)?> 
								</h4>
								<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 	<?=date("d/m/Y H:i",strtotime($respuesta->timestamp))?></small></p>
							</div>
							<div class="timeline-body">
								<p><?=$respuesta->respuesta?></p>
								<?php if(!empty($respuesta->file)){ ?>
									<hr>
									<div class="btn-group">
										<a class="btn btn-primary" href="<?=base_url('/upload/consultas/'.$respuesta->file)?>" target="_blank" role="button"><i class="glyphicon glyphicon-save"></i> Ver Archivo</a>
									</div>
								<?php } ?>
							</div>
						</div>
					</li>
				<?php } ?>
			</ul>	
		</div>
	</div>
	

	<div class="col-xs-12">
		<form action="<?=base_url('inquilinos/respuesta_consultas_create')?>" method="POST" enctype="multipart/form-data" >
			<div class="panel panel-default">
			  <div class="panel-heading" style="display:list-item;">
			  	<div class="col-sm-6">
			  		<label for="detalle_id"><?=ucfirst('ingrese una respuesta');?></label>
			  	</div>
			  	<div class="col-sm-6">
			  		<input type="file" class="file pull-right" style="color: transparent" >
			  	</div>
			  </div>
			  <div class="panel-body">
			  	<div class="form-group">
			  		<textarea class="form-control hint2basic" rows="6" placeholder="Respuesta" name="respuesta"></textarea>
			  	</div>
			  </div>
			</div>
			<input type="hidden"  name="consulta_id" value="<?=$consulta->id?>">
			<button type="submit" class="btn btn-mk-primary"><i class="glyphicon glyphicon-send"></i> Enviar</button>
		</form>
	</div>
</div>
	