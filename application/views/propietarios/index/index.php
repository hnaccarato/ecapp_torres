<div class="panel panel-default">
  <div class="panel-body">
 	<div class="row">
 		<?php if($this->session->flashdata('message')):?>
 		<div class="col-md-12">
 			<div class="alert alert-success" role="alert">
 				<?=$this->session->flashdata('message')?>
 			</div>
 		</div>
 		<?php endif ;?>
 	    <div class="col-md-4 row-eq-height">
 	    	<div class="caption">
 	    	    <address style="border-bottom: 3px solid;">
 	    	        <h4><strong><?=$user->first_name.' '.$user->last_name?></strong></h4>
 	    	    </address>
 	    	</div>
 	    		
 	    	<div class="row">
 	    		<div class="col-md-12">
 	    			<figure>
 	    				<img class="img-responsive" alt="<?=$edificio->nombre;?>" src="<?=base_url('upload/edificios/'.$edificio->imagen)?>">
 	    			</figure>
 	    		</div>
 	    		<div class="col-md-12">

 	    			<ul class="list-group">
 	    				<li class="list-group-item">
 	    					<strong><?=$edificio->nombre?></strong>
 	    				</li>
 	    				<li class="list-group-item">
 	    					<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp<small><?=$edificio->direccion?></small> 								
 	    				</li>
 	    				<li class="list-group-item">
 	    					<i class="fa fa-phone"></i>&nbsp<?=$edificio->telefono?>
 	    				</li>
 	    				<li class="list-group-item">
 	    					<img src="https://img.icons8.com/ios-glyphs/15/000000/door-closed.png">&nbspUnidad: <?=$unidad->name?> - <?=$unidad->departamento?>
 	    				</li>
 	    			</ul>
 	    		</div>
 	    	</div>
 	    </div>
 	    <div class="col-md-8 row-eq-height">
 	    	<div class="monthly" id="mycalendar"></div>
 	    </div>
 	</div>
  </div>
</div>

<script type="text/javascript">
	var sampleEvents = <?=$jason_date?>	

	$(document).ready(function(){
		$('#mycalendar').monthly({
			mode: 'event',
			dataType: 'json',
			events: sampleEvents
		});
	});

</script>