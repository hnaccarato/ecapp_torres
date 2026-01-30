
<div class="row">
	<div  class="col-xs-12 col-md-12">
		<label>Comentario</label>
		<p><strong style="text-align: center;color: red;"><?=$expensa->detalle?></strong></p>
	</div>	
</div>

<div class="row">
	<div class="col-xs-12 col-md-6"><?=ucfirst('Expensas');?></div>
	<div class="col-xs-12 col-md-6"><?=ucfirst($expensa->titulo)?></div>
	<div class="col-xs-12 col-md-6"><?=ucfirst('Fecha');?></div>
	<div class="col-xs-12 col-md-6"><?=ucfirst($expensa->fecha_pago)?></div>	
	<div class="col-xs-12 col-md-6"><?=ucfirst('Unidades');?></div>
	<div class="col-xs-12 col-md-6"><?=ucfirst($expensa->unidad)?></div>	
	<div class="col-xs-12 col-md-6"><?=ucfirst('Propietario');?></div>
	<div class="col-xs-12 col-md-6">
		<?=ucfirst($expensa->nombre)?> - <?=ucfirst($expensa->apellido)?>	
	</div>		
	<div class="col-xs-12 col-md-6"><?=ucfirst('Comprobante');?></div>
	 <?php if (!empty($expensa->mp_merchant_order_id)) { ?>
		<div class="col-xs-12 col-md-6">
			<a href="<?=base_url('upload/comprobante/'.$expensa->comprobante)?>"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
		</div>
	<?php } ?>
	<div class="col-xs-12 col-md-6"><?=ucfirst('nuevo Comprobante');?></div>
	<div class="col-xs-12 col-md-6">
		<div class="form-group">
			<div class="form-group">
			  <label for="exampleInputFile">Comprobante</label>
			  <input type="file" id="exampleInputFile" name="comprobante">
			  <p class="help-block">Adjuntar comprobante</p>
			</div>
		  	<input type="hidden" name="pago_id" value="<?=$expensa->id?>">
		</div>
	</div>	
</div>
