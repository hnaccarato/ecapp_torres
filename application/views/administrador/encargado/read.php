<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="encargado.legajo"><?=ucfirst('legajo');?></td>
            <td data-column="encargado.id"><?=ucfirst('Cargo');?></td>
            <td data-column="encargado.nombre"><?=ucfirst('nombre');?></td>
            <td data-column="encargado.telefono"><?=ucfirst('Teléfono');?></td>
            <td data-column="encargado.horario"><?=ucfirst('día y horario');?></td>
            <td data-column="encargado.medicina"><?=ucfirst('Obra Social');?></td>
            <td data-column="encargado.ropa"><?=ucfirst('Uniforme');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->legajo;?></td>
            <td><?=$register->cargo;?></td>
            <td><?=$register->nombre;?></td>
            <td><?=$register->telefono;?></td>
            <td><?=$register->horario;?></td>
            <td><?=$register->medicina;?></td>
            <td><?=$register->ropa;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li><a href="<?=base_url();?>administrador/encargado_update/<?=$register->id;?>" title="Editar Personal"><i class="fa fa-pencil"></i></a></li>
                    <li><a href="<?=base_url();?>administrador/encargado_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Personal"><i class="fa fa-times"></i></a></li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
