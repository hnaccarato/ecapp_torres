<?php
$btn = array(1=>'btn-success',5=>'btn-warning',6=>'btn-danger' );
?>

<table class="table mc-read-table table-responsive" id="table-read">
    <thead>
        <tr>
            <td data-column="reclamos.usaurio_id"><?=ucfirst('propietario');?></td>
            <td data-column="reclamos.descripcion"><?=ucfirst('titulo');?></td>
            <td data-column="reclamos.fecha"><?=ucfirst('fecha');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  class="sin_leer_<?=$register->estado_id?>" >
			<td><?=$register->nombre.' '.$register->apellido;?></td>
			<td><?=$register->detalle;?></td>
            <td><?=get_fecha($register->fecha);?></td>
            <td>
                <a href="<?=base_url();?>propietarios/consultas_view/<?=$register->id;?>" class="btn <?=$btn[$register->estado_id]?> btn-xs" title="Ver Consulta"><i class="glyphicon glyphicon-eye-open"></i> Ver
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
