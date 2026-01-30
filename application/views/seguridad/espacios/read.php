<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="espacios.id"><?=ucfirst('N°');?></td>
            <td data-column="espacios.nombre_espacio"><?=ucfirst('nombre espacio');?></td>
            <td data-column="espacios.init_hora"><?=ucfirst('desde');?></td>
            <td data-column="espacios.fin_hora"><?=ucfirst('hasta');?></td>  
            <td data-column="espacios.max"><?=ucfirst('Dias por periodo');?></td>
            <td data-column="espacios.max_meses"><?=ucfirst('Maximo de días');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>" id="<?=$register->id;?>">
			<td><?=$register->id;?></td>
            <td><?=$register->nombre_espacio;?></td>
            <td><?=$register->init_hora;?></td>
			<td><?=$register->fin_hora;?></td>
			<td><?=$register->max;?></td>
			<td><?=$register->max_meses;?></td>
            <td>
                <ul class="mc-actions-list">                  
                    <li>
                        <a href="<?=base_url();?>seguridad/espacios_load/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" title="Reservar" >
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
