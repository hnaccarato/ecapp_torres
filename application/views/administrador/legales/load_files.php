<?php foreach($files->result() as $value): ?>
	<div class="col-md-3" id="<?=$value->id?>">
		<div class="panel panel-default">
			
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-10">
						<?=ucfirst($value->name)?>
					</div>
					<div class="col-md-2">
					<a href="" class="remove_image" data-id="<?=$value->id?>" style="color: red">
						<strong>X</strong>
					</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="thumbnail">
                    <a href="<?=base_url('/upload/seguros/'.$value->file)?>" target="_blank" class="glyphicon glyphicon-save"> DESCARGAR</a>
				</div>
			</div>
		</div>
	</div>
<?php endforeach ?>