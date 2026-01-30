<style type="text/css">
	.gastos_titulo{
		background-color: gainsboro;
		display: flex;
		margin-bottom: 18px;
		margin-top: 3px;
	}

</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Expensas: <?=$values->titulo;?></h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>inquilinos/expensas_list/">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

<div class="panel panel-default">
	<div class="panel-heading panel-heading-custom">
		<h3 class="panel-title"><?=$values->titulo;?></h3>
	</div>
	<div class="panel-body">
		<h3>Expensas</h3>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<?=$values->descripcion;?>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-xs-12 col-md-4">
				<div class="row">
				  	<div class="col-md-8"><p><strong>Liquidación de Expensas</strong></p></div>
				  	<div class="col-md-4">                                            
						<?php if(!empty($values->file)){?>
						<a  href="<?=base_url("upload/expensas/".$values->file)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>				  	
					<div class="col-md-8"><p><strong>Prorrateo de Expensas</strong></p></div>
				  	<div class="col-md-4">                                            
						<?php if(!empty($values->prorrateo)){?>
						<a  href="<?=base_url("upload/expensas/".$values->prorrateo)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>				  	
					<div class="col-md-8"><p><strong>Gastos Particulares</strong></p></div>
				  	<div class="col-md-4 pull-left">                                            
						<?php if(!empty($values->gparticulares)){?>
						<a  href="<?=base_url("upload/expensas/".$values->gparticulares)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
				<div class="row">
				  	<div class="col-md-8"><p><strong>Rendición de cuentas auditadas</strong></p></div>
				  	<div class="col-md-4">                                            
						<?php if(!empty($values->anexo1)){?>
						<a  href="<?=base_url("upload/expensas/".$values->anexo1)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>				  	
				  	<div class="col-md-8"><p><strong>Estado de situación patrimonial</strong></p></div>
				  	<div class="col-md-4">                                            
						<?php if(!empty($values->anexo2)){?>
						<a  href="<?=base_url("upload/expensas/".$values->anexo2)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>				  	
					<div class="col-md-8"><p><strong>Libro de Sueldos</strong></p></div>
				  	<div class="col-md-4">                                            
						<?php if(!empty($values->lsueldo)){?>
						<a  href="<?=base_url("upload/expensas/".$values->lsueldo)?>" 
						target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
			  	<div class="col-md-8"><p><strong>Extracto Bancario</strong></p></div>
			  	<div class="col-md-4">                                            
					<?php if(!empty($values->ebancarios)){?>
					<a  href="<?=base_url("upload/expensas/".$values->ebancarios)?>" 
					target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
					<?php } ?>
				</div>	
			</div>
		</div>
		<hr>
		<h4>
			<u>
				<strong>Destalles de Gastos</strong> 
				<span class="small"><?=$values->titulo;?></span>
			</u>
		</h4>
		<div class="row gastos">
			<?php $flag = 0; ?>
			<?php foreach ($gastos->result() as $gasto) { 
				if($flag != $gasto->tipo_gasto_id){
					echo "<div class='col-md-12 gastos_titulo'><hr><h4><strong>".$gasto->tipo."</strong></h4></div>";
				} ?>
				<div class="col-md-4">
					<div class="row">
						<div class="col-md-8"><p><strong><?=$gasto->titulo?></strong></p></div>
						<div class="col-md-4">
							<?php if(!empty($gasto->comprobante)){?>
							<a  href="<?=base_url("upload/gastos/".$gasto->comprobante)?>" 
							target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php $flag = $gasto->tipo_gasto_id;?>
			<?php } ?>
		</div>
	</div>
</div>
</div>