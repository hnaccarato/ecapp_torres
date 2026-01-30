<style type="text/css">
.panel-heading{
    text-align: center;
    font-weight: 800;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Edicion de Expensas</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/expensas_list/">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <form method="POST" name="recibos_form" id="recibos_form" enctype="multipart/form-data" autocomplete="off">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="edificio_id_id"><?=ucfirst('edificio');?></label>
                            <select class="form-control" name="edificio_id" required>
                                <option value="">Seleccione</option> 
                                <?php foreach ($edificios->result() as $edificio) {?>
                                <option value="<?=$edificio->id?>" <?=($edificio->id == $values->edificio_id)? "selected" : "" ?> ><?=$edificio->nombre?></option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div>  

                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="usuarios_id"><?=ucfirst('usuarios');?></label>
                            <select class="form-control" name="usuarios_id" >
                                <option>Seleccione</option> 
                                <?php foreach ($usuarios->result() as $usuario) {?>

                                <option value="<?=$usuario->id?>" <?=($usuario->id == $values->usuarios_id)? "selected" : "" ?>><?=$usuario->first_name?><?=$usuario->last_name?></option> 
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="titulo_id"><?=ucfirst('titulo');?></label>
                            <input type="text" class="form-control" id="titulo_id" placeholder="titulo" name="titulo" value="<?=$values->titulo;?>" required>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="fecha_id"><?=ucfirst('fecha');?></label>
                            <input type="text" class="form-control datepicker" id="fecha_id" placeholder="fecha" name="fecha" value="<?=$values->fecha;?>" required>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-xs-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">Expensas</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <input type="file" class="form-control" id="file_id" placeholder="file" name="file">
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <?php if(!empty($values->file)){?>
                                            <a  href="<?=base_url("upload/expensas/".$values->file)?>" 
                                                target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga</span></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Prorrateo</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <input type="file" class="form-control" id="prorrateo" placeholder="prorrateo" name="prorrateo">
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <?php if(!empty($values->prorrateo)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->prorrateo)?>" 
                                                    target="_blank">
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Libro de Sueldos</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <input type="file" class="form-control" id="lsueldo" name="lsueldo">
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <?php if(!empty($values->lsueldo)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->lsueldo)?>" 
                                                    target="_blank">
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Extracto Bancario</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="ebancarios" 
                                                name="ebancarios">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <?php if(!empty($values->ebancarios)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->ebancarios)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>                            

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Anexo I</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="anexo1" 
                                                name="anexo1">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <?php if(!empty($values->anexo1)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->anexo1)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>                            

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Anexo II</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="anexo2" 
                                                name="anexo2">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <?php if(!empty($values->anexo2)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->anexo2)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>                            

                            <div class="col-xs-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading">Gastos Particulares</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-12"> 
                                                <input type="file" class="form-control" 
                                                id="gparticulares" 
                                                name="gparticulares">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <?php if(!empty($values->gparticulares)){ ?>
                                                <a href="<?=base_url("upload/expensas/".$values->gparticulares)?>" 
                                                    target="_blank">         
                                                    <span class="glyphicon glyphicon-save-file">
                                                        Descarga
                                                    </span>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="descripcion_id"><?=ucfirst('descripcion');?></label>
                                <textarea type="text" rows="10" class="form-control" id="descripcion_id" name="descripcion" ><?=$values->descripcion;?></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-mk-primary pull-right">Submit</button>

                </form>

            </div>
        </div>
    </div>  

    <script type="text/javascript">
        $(document).ready(function (){
            $('.datepicker').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });
        })
    </script>