
<div class="row">
  <?php foreach($registers->result() as $register){ ?>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                   <strong><?=$register->cargo;?></strong>
                </div>
                <div class="panel-body" style="height: 400px !important;">
                    <div class="card">
                        <a href="<?=base_url();?>propietarios/encargado_view/<?=$register->id;?>">
                            <img class="img-responsive img-thumbnail  center-block" src="<?=(is_file(BASEPATH.'../upload/encargado/'.$register->foto))? base_url('upload/encargado/'.$register->foto):base_url('access/images/user.png')?>" alt="stack photo" class="img" style="height: 200px !important;">
                        </a>
                        <div class="card-block">
                            <p>
                                <i class="glyphicon glyphicon-list-alt"></i> <?=$register->nombre;?>
                                <br />                                                               
                                <i class="glyphicon glyphicon-envelope"></i> 
                                    <a href="mailto:<?=$register->email;?>"><?=$register->email;?></a> 
                                <br />                            
                                <i class="glyphicon glyphicon-earphone"></i> <a href="tel:<?=$register->telefono;?>"><?=$register->telefono;?></a> 
                                <br />                            
                                <i class="glyphicon glyphicon-time"></i> <?=$register->horario;?>
                                <br />                                                              
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="icon pull-right">
                                <a href="<?=base_url();?>propietarios/encargado_view/<?=$register->id;?>" class="btn btn-info btn-xs" title="Ver Encargado">
                                    <i class="fa fa-eye fa-fw fa-2x" aria-hidden="true"></i> Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  <?php } ?>
</div>
