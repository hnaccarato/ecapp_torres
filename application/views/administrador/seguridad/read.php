<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td ><?=ucfirst('Nº');?></td>
            <td data-column="users.first_name"><?=ucfirst('Nombre');?></td>
            <td data-column="users.last_name"><?=ucfirst('Apellido');?></td>
            <td data-column="users.email"><?=ucfirst('email');?></td>
            <td data-column="users.phone"><?=ucfirst('Telefono');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1 ?>
        <?php foreach($registers->result() as $register){ ?>

        <tr  id="<?=$register->id;?>">
			<td><?=$i;?></td>
            <td><?=$register->first_name;?></td>
            <td><?=$register->last_name;?></td>
			<td><?=$register->email;?></td>
            <td><?=$register->phone;?></td>	
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/seguridad_update/<?=$register->id;?>" title="Editar Seguridad">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/seguridad_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Reglamento">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>                    
                </ul>
            </td>
        </tr>
        <?php $i ++?>
        <?php } ?>
    </tbody>
</table>
