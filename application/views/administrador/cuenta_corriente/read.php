<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="unidades.name"><?=ucfirst('Unidad');?></td>
            <td data-column="unidades.departamento"><?=ucfirst('departamento');?></td>
            <td data-column="unidades.recibos"><?=ucfirst('Reibos mes anterior');?></td>
            <td data-column="unidades.cuenta_corriente"><?=ucfirst('Cuenta corriente');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->unidad;?></td>
            <td><?=$register->departamento;?></td>
            <td><a href="<?=base_url('upload/cuenta_corriente/'.$register->file_1)?>">Descargar Recibo</a></td>
            <td><a href="<?=base_url('upload/cuenta_corriente/'.$register->file_2)?>">Descargar cuenta</a></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/cuenta_corriente_update/<?=$register->id;?>" title="Editar registro">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/cuenta_corriente_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar registro">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
