<div class="row">
	<div class="col-xs-12 col-sm-7">
	    <h2 class="mc-page-header">ver reclamo</h2>
	</div>
	<div class="col-xs-12 col-sm-5">
	    <ul class="mc-page-actions">
	        <li>
	            <a type="button" href="<?=base_url();?>propietarios/reclamos_list"><i class="fa fa-arrow-circle-o-left"></i></a>
	        </li>
	    </ul>
	</div>
	<div class="col-md-12">
		<div class="panel panel-default panel-primary">
		  <div class="panel-heading "><?=$reclamo->fecha.' '.$reclamo->detalle?></div>
		  <div class="panel-body">
		  	<h5><?=$reclamo->nombre." ".$reclamo->apellido?>:</h5>
		  	<?=$reclamo->descripcion?>
		  </div>
		</div>
	</div>	
	<?php foreach ($reclamos->result() as $value) {?>
		
		<div class="col-md-12">
			<div class="panel panel-default <?=($value->is_admin==true)? "panel-danger":"panel-info" ?>">
			  <div class="panel-heading "><?=$reclamo->fecha.' '.$reclamo->detalle?></div>
			  <div class="panel-body">
			  	<h5><?=$value->nombre." ".$value->apellido?>:</h5>
			  	<?=$value->respuesta?>
			  </div>
			</div>
		</div>

	<?php }?>

    <div class="col-xs-12">
   		<form action="<?=base_url('propietarios/respuesta_reclamos_create')?>" method="POST" name="reclamos_form" id="reclamos_form">
   		<div class="row">
            <div class="col-xs-12 col-sm-12">
        		<div class="form-group">
        	        <label for="detalle_id"><?=ucfirst('respuesta');?></label>
        	        <textarea class="form-control hint2basic" rows="6" placeholder="respuesta" name="respuesta"></textarea>
        	    </div>
            </div>
   		</div>
   		<input type="hidden"  name="reclamo_id" value="<?=$reclamo->id?>">
   		<button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
   		</form>
    </div>
</div>