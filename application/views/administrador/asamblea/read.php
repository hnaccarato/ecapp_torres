<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="asambleas.id"><?=ucfirst('N°');?></td>
            <td data-column="asambleas.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="asambleas.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="asambleas.detalle"><?=ucfirst('detalle');?></td>
            <td data-column="asambleas.estado_id"><?=ucfirst('estado');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
           
            <td><?=$register->id;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=$register->fecha_envio;?></td>
            <td><?=$register->detalle;?></td>
            <td><?=$register->estado;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/asamblea_update/<?=$register->id;?>" title="Editar Asamblea"><i class="fa fa-pencil"></i></a>
                    </li>

                    <li>
                        <a href="<?=base_url();?>administrador/asamblea_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete"><i class="fa fa-times"></i></a>
                    </li>
                </ul>
            </td>

        </tr>
        <?php } ?>
    </tbody>
</table>
