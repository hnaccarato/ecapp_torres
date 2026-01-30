
<div class="row">
    <div class="col-xs-12 col-sm-7">
        <h2 class="mc-page-header">Votar Encuesta</h2>
    </div>
    <div class="col-xs-12 col-sm-5">
        <ul class="mc-page-actions">
            <li>
                <a type="button" href="<?=base_url();?>propietarios/propuestas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
            </li>
        </ul>
    </div>
</div>
<hr class="hr_subrayado">
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <h4><i class="glyphicon glyphicon-ok-circle"></i> <?=$propuesta->titulo;?></h4>
            </div>
            <div class="col-md-6">
                <h4><i class="glyphicon glyphicon-calendar"></i> <?=$propuesta->fecha_fin;?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-left"><?=ucfirst($propuesta->descripcion);?></h3>
            </div>
        </div>
    </div>
    <hr>
    <form action="<?=base_url('propietarios/propuesta_votar')?>" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <div class="panel-heading">
                        <p class="text-center"><strong><?=ucfirst('Opciones');?></strong></p>
                    </div>
                    <ul class="list-group">
                        <?php foreach ($opciones->result() as  $value) { ?>
                            <?php if(!empty($value->titulo)) {?>
                                <li class="list-group-item">
                                    <?=$value->titulo?>
                                    <div class="material-switch pull-right">
                                        <input id="radio_<?=$value->id?>" name="opcion" type="radio" value="<?=$value->id?>" required/>
                                        <label for="radio_<?=$value->id?>" class="label-default"></label>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        <input type="hidden" name="propuesta_id" value="<?=$propuesta->id?>">
                    </ul> 
                </div>  
            </div>
            <div class="col-md-6">
                <div class="panel-heading">
                    <p class="text-center"><strong><?=ucfirst('Archivos');?></strong></p>
                </div>
                <ul class="list-group">
                    <?php foreach ($files->result() as  $value) { ?>
                        <li class="list-group-item"><a href="<?=base_url('/upload/propuesta/'.$value->file)?>" target="_blank" class="glyphicon glyphicon-save"> Descargar</a></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="col-md-12 text-center" style="margin-bottom: 23px">
                <hr>
                <button type="submit" class="btn btn-mk-primary">Votar</button>
            </div>
        </div>

    </form>
</div>


