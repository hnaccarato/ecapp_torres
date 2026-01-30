<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="mc-page-header">Circular</h2>
        </div>
        <div class="col-xs-12">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/circular_list/">
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
                <div class="col-md-12"><p class="text-center"><h4><?=$circular->titulo?></h4></p></div>
                <div class="col-md-3"><h5>Fecha de la operacion</h5></div>
                <div class="col-md-9">
                    <p class="text-left">
                        <?=$circular->fecha_envio?>
                    </p>
                </div>
                <div class="col-md-12">
                    <p class="text-justify">
                        <?=$circular->detalle?>
                    </p>
                </div>
                <div class="col-md-12">
                    <?php if(!empty($circular->file)){ ?>
                        <p class="text-left"><?=ucfirst("Descaragar archivo")?> 
                        <a href="<?=base_url('/upload/circular/'.$circular->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                    </p>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
</div>