<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="espacios.id"><?=ucfirst('N°');?></td>
            <td data-column="espacios.nombre_espacio"><?=ucfirst('Amenity');?></td>
            <td data-column="espacios.init_hora"><?=ucfirst('desde');?></td>
            <td data-column="espacios.fin_hora"><?=ucfirst('hasta');?></td>  
            <td data-column="espacios.max"><?=ucfirst('Reservas permitidas por UF');?></td>
            <td data-column="espacios.max_meses"><?=ucfirst('Días de anticipación');?></td>
            <td data-column="espacios.actve"><?=ucfirst('Activo');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>" id="<?=$register->id;?>">
			<td><?=$register->id;?></td>
            <td><?=$register->nombre_espacio;?></td>
            <td><?=$register->init_hora;?></td>
			<td><?=$register->fin_hora;?></td>
			<td><?=$register->max;?></td>
			<td><?=$register->max_meses;?></td>
            <td><?=($register->active == true)? 'Si':'No';?></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/espacios_update/<?=$register->id;?>"  title="Editar Espacio"><i class="fa fa-pencil"></i>
                        </a>
                    </li>
                    <?php if($this->espacios_model->reservas_habilitado($register->id)): ?>
                    <li>
                        <a href="<?=base_url();?>administrador/espacios_load/<?=$register->id;?>" data-primary-key="<?=$register->id;?>"  title="Hacer una reserva">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php endif ?>
                    <li>
                        <a href="<?=base_url();?>administrador/espacios_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Espacio">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>     
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
