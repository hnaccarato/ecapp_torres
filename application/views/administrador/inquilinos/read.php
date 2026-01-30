<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="edificios.nombre"><?=ucfirst('unidad');?></td>
            <td data-column="users.first_name"><?=ucfirst('Inquilino');?></td>
            <td data-column="propuestas.fecha_fin"><?=ucfirst('email');?></td>
            <td data-column="propuestas.fecha_fin"><?=ucfirst('Telefono');?></td>
            <td data-column="propuestas.sector"><?=ucfirst('activo');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->unidad_nombre;?></td>
			<td><?=$register->nombre;?> <?=$register->apellido;?></td>
			<td><?=$register->email;?></td>
			<td><?=$register->phone;?></td>
			<td><?=$register->active;?></td>
            <td>
                <a href="<?=base_url('administrador/inquilino_update/'.$register->id)?>" class="btn btn-success btn-xs view" title="Editar Inquilino" ><i class="fa fa-pencil"></i> Editar</a>
                
                <a href="<?=base_url('administrador/consultas_create/'.$register->id)?>" class="btn btn-success btn-xs view" title="Enviar Mensaje" ><i class="fa fa-comments"></i> Mensaje</a>

                <a href="<?=base_url();?>administrador/inquilino_delete/<?=$register->users_unidad_id;?>" data-primary-key="<?=$register->id;?>" class="delete btn btn-danger btn-xs" title="Eliminar Inquilino">
                <i class="glyphicon glyphicon-remove"></i> Eliminar
                </a>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
