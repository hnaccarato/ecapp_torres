<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="unidades.name"><?=ucfirst('Unidad');?></td>
            <td data-column="unidades.departamento"><?=ucfirst('departamento');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->name;?></td>
            <td><?=$register->departamento;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/unidad_update/<?=$register->id;?>" title="Editar Unidad">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/unidad_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Unidad">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
