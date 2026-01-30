<?php
$btn = array(1=>'btn-warning',5=>'btn-success sin_leer',6=>'btn-danger' );
?>
<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="consultas.id"><?=ucfirst('N°');?></td>
            <td data-column="consultas.id"><?=ucfirst('Departamento');?></td>
            <td data-column="users.first_name"><?=ucfirst('Nombre');?></td>
            <td data-column="consultas.descripcion"><?=ucfirst('titulo');?></td>
            <td data-column="consultas.fecha"><?=ucfirst('fecha');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>" class="sin_leer_<?=$register->estado_id?>">
            <td><?=$register->id?></td>
            <td><?=$register->unidad?> - <?=$register->departamento?></td>
			<td><?=$register->nombre.' '.$register->apellido;?></td>
            <td><?=$register->detalle;?></td>
            <td><?=get_fecha($register->fecha);?></td>
            <td>
                <a href="<?=base_url();?>administrador/consultas_view/<?=$register->id;?>" class="btn <?=$btn[$register->estado_id]?> btn-xs"  title="Ver Consulta"><i class="glyphicon glyphicon-eye-open"></i> Ver </a>

                <a href="<?=base_url();?>administrador/consultas_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-danger btn-xs delete" title="Eliminar Consulta"><i class="fa fa-times"></i> Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
