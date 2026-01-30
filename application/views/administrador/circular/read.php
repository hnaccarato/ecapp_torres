<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="circular.id"><?=ucfirst('n°');?></td>
            <td data-column="circular.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="circular.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="circular.detalle"><?=ucfirst('detalle');?></td>
            <td data-column="circular.estado_id"><?=ucfirst('Estado');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
           
            <td><?=$register->id;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=$register->fecha_envio;?></td>
            <td><?=substr($register->detalle, 0, 24);?>..</td>
            <td><?=$register->estado;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li><a href="<?=base_url();?>administrador/circular_update/<?=$register->id;?>" title="Editar Circular"><i class="fa fa-pencil"></i></a></li>                    
                    <li>
                        <a href="<?=base_url();?>administrador/circular_view/<?=$register->id;?>" title="Ver traking circular">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/circular_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Circular">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>   
                </ul>
            </td>

        </tr>
        <?php } ?>
    </tbody>
</table>
