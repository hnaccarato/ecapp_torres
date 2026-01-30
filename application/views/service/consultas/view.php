<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Consultas</title>

	<link href="<?=base_url();?>css/bootstrap.css" rel="stylesheet">
  <link href="<?=base_url();?>css/business-frontpage.css" rel="stylesheet">

</head>
<body>
	<header class="business-header">
		<div class="container">
			<img src="<?=base_url('images/logo.png')?>" class="logo" data-pin-nopin="true">
		</div>
	</header>

	<!-- Page Content -->
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-7">
				<h3 class="mc-page-header">Consulta</h3>
			</div>
			<div class="col-xs-12 col-sm-5">
				<ul class="mc-page-actions">
					<li>
						<a type="button" href="<?=base_url();?>propietarios/consultas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
					</li>
				</ul>
			</div>
			<hr>
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="well">
							<h4><p class="text-center"><?=ucfirst($consulta->detalle)?></p></h4>
							<h4>
								<p class="text-center">
									<?=ucfirst($consulta->nombre." ".$consulta->apellido)?> 
									<small><?=fechaCastellano($consulta->fecha)?></small>
								</p>
							</h4>
							<p class="text-justify"><?=ucfirst($consulta->descripcion)?></p>
						</div>
						<?php if(!empty($consulta->file)){ ?>
						<p class="text-right"><?=ucfirst("Descaragar archivo")?> 
							<a href="<?=base_url('/upload/consultas/'.$consulta->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
						</p>
						<?php } ?>
						<?php foreach ($respuestas->result() as $respuesta) {?>

						<div class="media <?=($respuesta->is_admin == 1)? 'media_admin':'' ?>  "> 
							<div class="media-left"> 
								<a href="#"> 
									<img alt="64x64" class="media-object img-circle" src="<?=base_url(($respuesta->is_admin)? '/images/adm.png':'/images/user.png')?>" data-holder-rendered="true" style="width: 64px; height: 64px;">
								</a> 
							</div> 
							<div class="media-body"> 
								<h4 class="media-heading">
									<?=ucfirst($respuesta->nombre." ".$respuesta->apellido)?> 
									<small><?=fechaCastellano($respuesta->fecha)?></small>
								</h4> 
								<p class="text-align"><?=$respuesta->respuesta?></p>
								<?php if(!empty($respuesta->file)){ ?>
								<p class="text-right"><?=ucfirst("Descaragar archivo")?> 
									<a href="<?=base_url('/upload/consultas/'.$respuesta->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
								</p>
								<?php } ?>

							</div> 
						</div> 
						<hr>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-xs-12">
				<form action="<?=base_url('propietarios/respuesta_consultas_create')?>" method="POST" enctype="multipart/form-data" >
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
								<textarea class="form-control" rows="6" placeholder="Respuesta" name="respuesta"></textarea>
							</div>
						</div>
					</div>
					<input type="hidden"  name="consulta_id" value="<?=$consulta->id?>">
					<button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
				</form>
			</div>
		</div>
	</div>
</body>
    <script src="<?=base_url();?>js/jquery-1.11.1.min.js"></script>
    <script src="<?=base_url();?>js/jquery-ui.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url();?>js/bootstrap.js"></script>
    <SCRIPT TYPE="text/javascript">
        base_url = '<?=base_url()?>';
        controlador = 'administrador';
    </SCRIPT>
</html>
