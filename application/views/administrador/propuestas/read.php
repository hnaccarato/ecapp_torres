<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="propuestas.id"><?=ucfirst('N°');?></td>
            <td data-column="users.first_name"><?=ucfirst('Usuario');?></td>
            <td data-column="propuestas.fecha_fin"><?=ucfirst('fecha_fin');?></td>
            <td data-column="propuestas.titulo"><?=ucfirst('Titulo');?></td>
            <td data-column="propuestas.estado_id"><?=ucfirst('estado');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
						<td><?=$register->id;?></td>
						<td><?=$register->nombre;?> <?=$register->apellido;?></td>
						<td><?=$register->fecha_fin;?></td>
						<td><?=$register->titulo;?></td>
						<td><?=$register->estado;?></td>

            <td>
                <ul class="mc-actions-list">
                    <li><a href="<?=base_url();?>administrador/propuestas_update/<?=$register->id;?>" title="Editar Votación"><i class="fa fa-pencil"></i></a></li>
       
                    <li>
                        <a href="<?=base_url();?>administrador/propuestas_votacion/<?=$register->id;?>" title="Ver Votación" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li><a href="<?=base_url();?>administrador/propuestas_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Votación"><i class="fa fa-times"></i></a></li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
