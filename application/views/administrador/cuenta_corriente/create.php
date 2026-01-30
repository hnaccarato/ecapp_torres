<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Nueva - Cuentas corrientes y recibos</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">b
                <li>
                    <a type="button" href="<?=base_url();?>administrador/cuenta_corriente_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">
    <div class="panel panel-default">
        <div class="panel-body">
            <hr>    
            <div class="row">    
                <div class="col-xs-12">
                    <form method="POST" name="edificios_form" id="edificios_form" enctype="multipart/form-data" autocomplete="off">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="unidad_id"><?=ucfirst('Unidad funcional');?></label>
                                    <select class="form-control" name="unidad_id" id="unidad_id" required="required">
                                        <option>Seleccione</option> 
                                        <?php foreach ($unidades->result() as $unidades) {?>
                                            <option value="<?=$unidades->id?>">
                                                <?=$unidades->name?> - <?=$unidades->departamento?>
                                            </option> 
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>                    
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="file_1"><?=ucfirst('recibo de pago');?></label>
                                    <input type="file" class="form-control" name="file_1" accept=".jpg,.jpeg,.gif,.bmp,.gif">
                                </div>
                            </div>                    
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="file_2"><?=ucfirst('cuenta corriente');?></label>
                                    <input type="file" class="form-control" name="file_2" accept=".jpg,.jpeg,.gif,.bmp,.gif">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
