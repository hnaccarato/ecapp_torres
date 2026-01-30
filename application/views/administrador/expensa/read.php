<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="expensa.unidad"><?=ucfirst('unidad');?></td>
            <td data-column="expensa.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="expensa.saldo"><?=ucfirst('saldo');?></td>
            <td data-column="expensa.pagos"><?=ucfirst('pagos');?></td>
            <td data-column="expensa.deuda"><?=ucfirst('deuda');?></td>
            <td data-column="expensa.interes"><?=ucfirst('interes');?></td>
            <td data-column="expensa.gastos_1"><?=ucfirst('gastos_1');?></td>
            <td data-column="expensa.gastos_2"><?=ucfirst('gastos_2');?></td>
            <td data-column="expensa.gastos_3"><?=ucfirst('gastos_3');?></td>
            <td data-column="expensa.mantenimiento"><?=ucfirst('departamento');?></td>
            <td data-column="expensa.bar"><?=ucfirst('bar');?></td>
            <td data-column="expensa.gastos_particulares"><?=ucfirst('gastos_particulares');?></td>
            <td data-column="expensa.total"><?=ucfirst('total');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->unidad;?></td>
            <td><?=$register->fecha;?></td>
            <td><?=$register->saldo;?></td>
            <td><?=$register->pagos;?></td>
            <td><?=$register->deuda;?></td>
            <td><?=$register->interes;?></td>
            <td><?=$register->gastos_1;?></td>
            <td><?=$register->gastos_2;?></td>
            <td><?=$register->gastos_3;?></td>
            <td><?=$register->mantenimiento;?></td>
            <td><?=$register->bar;?></td>
            <td><?=$register->gastos_particulares;?></td>
            <td><?=$register->total;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/expensa_update/<?=$register->id;?>" title="Editar expensa">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?=base_url();?>administrador/expensa_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar expensa">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
