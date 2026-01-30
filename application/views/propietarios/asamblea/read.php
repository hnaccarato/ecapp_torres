<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="cartelera.edificio_id"><?=ucfirst('edificio');?></td>
            <td data-column="cartelera.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="cartelera.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="cartelera.detalle"><?=ucfirst("Convocatoria y Poder")?></td>
            <td data-column="cartelera.detalle"><?=ucfirst("Memoria y Balance")?> </td>
            <td data-column="cartelera.detalle"><?=ucfirst('Acta de Asamblea');?></td>
            <td data-column="cartelera.detalle"><?=ucfirst('Presentación / Otros');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->edificio;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=$register->fecha_envio;?></td>
            <td> 
              <?php if(!empty($register->file)){ ?>
                <p class="col-sm-3"> 
                  <a href="<?=base_url('/upload/asamblea/'.$register->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>
              <?php } ?> 
            </td>      
            <td> 
              <?php if(!empty($register->memoria_balanse)){ ?>
                <p class="col-sm-3"> 
                  <a href="<?=base_url('/upload/asamblea/'.$register->memoria_balanse)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>
              <?php } ?> 
            </td>      
            <td> 
              <?php if(!empty($register->acta_asamblea)){ ?>
                <p class="col-sm-3"> 
                  <a href="<?=base_url('/upload/asamblea/'.$register->acta_asamblea)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>
              <?php } ?> 
            </td>             
            <td> 
              <?php if(!empty($register->acta_other)){ ?>
                <p class="col-sm-3"> 
                  <a href="<?=base_url('/upload/asamblea/'.$register->acta_other)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                </p>
              <?php } ?> 
            </td>            
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>propietarios/view_asamblea/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-info btn-xs" title="Ver Asamblea">
                            <i class="fa fa-eye" aria-hidden="true"></i> ver
                        </a>
                    </li>
                </ul>
            </td>

        </tr>
        <?php } ?>
    </tbody>
</table>
