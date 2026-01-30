<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Asamblea</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>propietarios/asamblea_list/">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

<hr class="hr_subrayado">
<div class="panel panel-default">
  <div class="panel-body">
  	<div class="row">
  		<div class="col-md-12"><p class="text-center"><h4><?=$asamblea->titulo?></h4></p></div>
  		<div class="col-md-3"><h5>Fecha de la Asamblea</h5></div>
  		<div class="col-md-9">
  			<p class="text-left">
  				<?=$asamblea->fecha_envio?>
  			</p>
  		</div>
  		<div class="col-md-12">
  			<p class="text-justify">
  				<?=$asamblea->detalle?>
  			</p>
		</div>
    <div class="col-xs-12 col-sm-12">
      <?php if(!empty($asamblea->file)){ ?>

        <p class="col-sm-3"><?=ucfirst("Convocatoria y Poder")?> 
          <a href="<?=base_url('/upload/asamblea/'.$asamblea->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
        </p>
        
      <?php } ?>            

      <?php if(!empty($asamblea->memoria_balanse)){ ?>

        <p class="col-sm-3"><?=ucfirst("Memoria y Balance")?> 
          <a href="<?=base_url('/upload/asamblea/'.$asamblea->memoria_balanse)?>" target="_blank" class="glyphicon glyphicon-save"></a>
        </p>

      <?php } ?>            

      <?php if(!empty($asamblea->acta_asamblea)){ ?>

        <p class="col-sm-3"><?=ucfirst("Acta de Asamblea")?> 
          <a href="<?=base_url('/upload/asamblea/'.$asamblea->acta_asamblea)?>" target="_blank" class="glyphicon glyphicon-save"></a>
        </p>

      <?php } ?>      
      <?php if(!empty($asamblea->acta_other)){ ?>

        <p class="col-sm-3"><?=ucfirst("Presentación / Otros")?> 
          <a href="<?=base_url('/upload/asamblea/'.$asamblea->acta_other)?>" target="_blank" class="glyphicon glyphicon-save"></a>
        </p>

      <?php } ?>

    </div>

  	</div>
  </div>
</div>
</div>