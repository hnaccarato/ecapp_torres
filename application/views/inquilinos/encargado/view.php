
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Ver - Personal</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>propietarios/encargado_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

<div class="panel panel-default">
    <div class="panel-heading"><h3><?=$values->cargo?></h3></div>
    <div class="panel-body">
    
        <div class="row">
            <div class="col-md-4">

                <img class="img-responsive img-thumbnail  center-block" src="<?=(is_file(BASEPATH.'../upload/encargado/'.$values->foto))? base_url('upload/encargado/'.$values->foto):base_url('access/images/user.png')?>" alt="stack photo" class="img">
             
            </div>
            <div class="col-md-8">
                <ul style="list-style:none;" class="card-block">
                    <li>
                        <p>
                            <label for="legajo"><?=ucfirst('legajo');?>: </label> <?=ucfirst($values->legajo);?>''
                        </p>
                    </li>  
                    <li>
                        <p>
                            <label for="nombre"><?=ucfirst('Nombre');?>: </label> <?=ucfirst($values->nombre);?>
                        </p>
                    </li>                         
               
                    <li>
                        <p>
                            <label for="email"><?=ucfirst('E-mail');?>: </label>  <a href="mailto:<?=$values->email;?>"><?=ucfirst($values->email);?></a> 
                        </p>
                    </li>    
                    <li>
                        <p>
                            <label for="telefono"><?=ucfirst('telefono / Celular');?>: </label> <a href="tel:<?=$values->telefono;?>"><?=ucfirst($values->telefono);?></a>
                        </p>
                    </li>             
              
                    <li>
                        <p>
                            <label for="horarios"><?=ucfirst('Día y horario');?>: </label> <?=ucfirst($values->horario);?>
                        </p>
                    </li>                
                    <li>
                        <p>
                        <label for="vacaciones"><?=ucfirst('vacaciones');?>: </label> <?=ucfirst($values->vacaciones)?>
                        </p>
                    </li>                    
                    <li>
                        <p>
                        <label for="uniforme"><?=ucfirst('uniforme');?>: </label> <?=ucfirst($values->ropa)?>
                        </p>
                    </li>
                    <li>
                        <p>
                            <label for="art"><?=ucfirst('art / Seguro');?>: </label>                                 
                            <?php if(!empty($values->art)){ ?>
                                <a  href="<?=base_url("upload/encargado/".$values->art)?>" 
                                target="_blank" id="expensa_file"><span class="glyphicon glyphicon-save-file">Descarga ART</span></a>
                            <?php } ?>
                        </p>
                    </li>                
            
                    <li>
                        <p>
                            <label for="seguros"><?=ucfirst('Tareas');?>: </label> <?=ucfirst($values->tarea)?>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>

