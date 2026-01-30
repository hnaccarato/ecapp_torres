<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="users.first_name"  width="30%"><?=ucfirst('Nombre');?></td>
            <td data-column="users.email"  width="30%"><?=ucfirst('email');?></td>
            <td data-column="users.phone" width="15%"><?=ucfirst('Telefono');?></td>
            <td data-column="unidades.name" width="15%"><?=ucfirst('Unidades');?></td>
            <td width="10%">Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1 ?>
        <?php foreach($registers->result() as $register){ ?>

        <tr  id="<?=$register->id;?>">
            <td><?=$register->first_name;?> <?=$register->last_name;?></td>
			<td><?=$register->email;?></td>
            <td ><?=$register->phone;?></td>
            <td ><?=$register->unidades;?></td>		
            <td >
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/propietarios_update/<?=$register->id;?>" title="Editar Propietario">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/inquilinos/<?=$register->id;?>" title="Agregar Inquilinos">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </a>
                    </li>                      
                    <li>
                        <a href="<?=base_url();?>administrador/consultas_create/<?=$register->id;?>" title="Enviar Mensaje">
                            <i class="fa fa-comments" aria-hidden="true"></i>
                        </a>
                    </li>                        
                    <li>
                        <a href="<?=base_url();?>administrador/users_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete"  title="Eliminar Propietario">
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
