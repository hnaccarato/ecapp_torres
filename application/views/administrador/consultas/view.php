<div class="row">
	<div class="col-xs-12 col-sm-7">
		<h3 class="mc-page-header">Consulta</h3>
	</div>
	<div class="col-xs-12 col-sm-5">
		<ul class="mc-page-actions">
			<li>
				<a type="button" href="<?=base_url();?>administrador/consultas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
			</li>
		</ul>
	</div>
</div>
<hr class="hr_subrayado">

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
									<?=fechaCastellano($consulta->fecha)?>
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
								<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?=fechaCastellano($respuesta->fecha)?></small></p>
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
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<form action="<?=base_url('administrador/respuesta_consultas_create')?>" method="POST" enctype="multipart/form-data" >
				<div class="panel-body">
					<h4 for="invitacion"><?=ucfirst('Ingrese una respuesta');?></h4>
					<hr>
				    <div class="col-md-12">
						<div class="form-group">
					        <label for="detalle_id"><?=ucfirst('descripcion');?></label>
					        <textarea class="form-control hint2basic" rows="6" placeholder="descripcion" name="descripcion"></textarea>
					    </div>
				    </div>

				    <hr>
				    <div class="row">
				    	<br>
    		    		<div class="col-md-6">
    	    				<div class="form-group">
    	    				<label for="autorizacion_id">
    	    					<?=ucfirst('Ingresar Archivo');?></label>
    	    					<input type="file" class="file" id="input_name" placeholder="Adjuntar archivo" name="file">
    	    				</div>
    	    			</div>
				    	<div class="col-md-6">		
				    		<label for="invitacion"><?=ucfirst('Renviar Consulta');?></label>
				    		<div class="row">
				    			<div class="col-md-6">
				    				<div class="form-group">
				    					<label for="input_name"><?=ucfirst('nombre');?></label>
				    					<input type="text" class="form-control" id="input_name" placeholder="Nombre">
				    				</div>
				    			</div>
				    			<div class="col-md-6">
				    				<label for="invitacion"><?=ucfirst('email');?></label>
				    				<div class="input-group">
				    					<input type="email" name="invitacion" id="invitacion" class="form-control" placeholder="email@email.com">
				    					<span class="input-group-btn">
				    						<button class="btn btn-secondary" type="button" id="send_invitacion">
				    							<i class="fa fa-paper-plane-o" aria-hidden="true"></i>
				    						</button>
				    					</span>
				    				</div>
				    			</div>
				    		</div>
				    	</div>
				    </div>
				    <hr>
				    <div class="row">
	    	    		<div class="col-md-6">
	    	    			<div class="form-group pull-left">
	    	    				<br>
		    	    			<input type="hidden"  name="consulta_id" value="<?=$consulta->id?>">
		    	    			<button type="submit" class="btn btn-mk-primary"><i class="glyphicon glyphicon-send"></i> Enviar</button>
	    	    			</div>
	    	    		</div>
				    	<div class="col-md-6">
				    		<div class="form-group pull-right">
				    			<label for="autorizacion_id">
				    				<?=ucfirst('Cerrar consulta');?></label>
			    				<select class="form-control" id="autorizacion_id" name="estado_id" required>
			    					<option value="<?=PENDIENTE?>">No</option>
			    					<option value="<?=CERRADO?>">Si</option>
		    					</select>
		    				</div>
			    		</div>

				    </div>
				</div>
			</form>
		</div>
	</div>
</div>

	<script type="text/javascript">
		$(document).ready(function(){
			$("#send_invitacion").click(function(e){
				e.preventDefault();
				var email = $("#invitacion").val();
				var name = $("#input_name").val();
				var consulta_id = <?=$consulta->id?>;

				if( name.length < 3 ){

					$.alert({
						title: 'Error!',
						content: 'Debe completar el nombre',
					});
					$("#input_name").focus();
					return false;
				}

				if(!validEmail(email)){

					$.alert({
						title: 'Error!',
						content: 'El email es incorrecto',
					});

					$("#invitacion").focus();
					return false;
				}


				$.post(base_url+'administrador/invitacion',
					{email:email,consulta_id:consulta_id,name:name},
					function(data){
						$.alert({
							title: 'Enviado',
							content: "Se notifico por mail a "+email,
						});
					});

			})
		})
		function validEmail(email) {
			var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
			return (email.match(r) == null) ? false : true;
		}
	</script>