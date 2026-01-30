<div class="row">
	<div class="col-xs-12 col-md-3"><?=ucfirst('Expensas :');?></div>
	<div class="col-xs-12 col-md-9"><?=ucfirst($expensa->titulo)?></div>
	<div class="col-xs-12 col-md-3"><?=ucfirst('Fecha :');?></div>
	<div class="col-xs-12 col-md-9"><?=ucfirst($expensa->fecha_pago)?></div>	
	<div class="col-xs-12 col-md-3"><?=ucfirst('Importe :');?></div>	
	<div class="col-xs-12 col-md-9"><?=ucfirst($expensa->importe)?></div>	
	<div class="col-xs-12 col-md-3"><?=ucfirst('Unidades :');?></div>
	<div class="col-xs-12 col-md-9"><?=ucfirst($expensa->unidad)?></div>	
	<div class="col-xs-12 col-md-3"><?=ucfirst('Propietario');?></div>
	<div class="col-xs-12 col-md-9">
		<?=ucfirst($expensa->nombre)?> - <?=ucfirst($expensa->apellido)?>	
	</div>	
	<div class="col-xs-12 col-md-3"><?=ucfirst('Comprobante');?></div>
	<div class="col-xs-12 col-md-9">
		<?php if(!empty($expensa->comprobante)){?>
			<a href="<?=base_url('upload/comprobante/'.$expensa->comprobante)?>">
				<span class="glyphicon glyphicon-save-file">Descarga</span>
			</a>
		<?php } ?>	
	</div>
	<div class="col-xs-12 col-md-3"><?=ucfirst('Seleccione el estado');?></div>
	<div class="col-xs-12 col-md-9">
		<div class="form-group">
		    <select class="form-control" name="estado_id" required="required">
		    	<?php foreach ($estados as $estado) { ?>
		    		<option value="<?=$estado->id?>" 
		    			<?=($estado->id == $expensa->estado_id)? "selected":"" ?> ><?=$estado->nombre?></option>
		    	<?php } ?>
		    </select>
		  	<input type="hidden" name="pago_id" value="<?=$expensa->id?>">
		</div>
	</div>	
	<div  class="col-xs-12 col-md-12">
		<label>Comentario</label>
		<p><strong><?=$expensa->detalle?></strong></p>
	</div>	
	<div class="col-xs-12 col-md-12">
		<div class="form-group">
			<label for="detalle"><?=ucfirst('Detalle');?></label>
		    <textarea class="form-control hint2basic" rows="6" placeholder="detalle" id="detalle" name="detalle"></textarea>
		</div>
	</div>	
</div>
