<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="reservas.id"><?=ucfirst('N°');?></td>
            <td data-column="espacios.nombre_espacio"><?=ucfirst('nombre espacio');?></td>
            <td data-column="reservas.dia_calendario"><?=ucfirst('fecha');?></td>
            <td data-column="reservas.hora_reserva"><?=ucfirst('desde');?></td>
            <td data-column="reservas.hora_hasta"><?=ucfirst('hasta');?></td>  
            <td data-column="espacios.max"><?=ucfirst('unidad');?></td>
            <td data-column="espacios.max"><?=ucfirst('Departamento');?></td>
            <td data-column="users.first_name"><?=ucfirst('Propietario');?></td>
            <td data-column="reservas.timestamp"><?=ucfirst('fecha');?></td>
            <td data-column="reservas.timestamp"><?=ucfirst('importe');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>" id="<?=$register->id;?>">
			<td><?=$register->id;?></td>
            <td><?=$register->nombre_espacio;?></td>
            <td><?=$register->date;?></td>
            <td><?=$register->desde;?></td>
			<td><?=$register->hasta;?></td>
            <td><?=$register->unidad;?></td>
			<td><?=$register->departamento;?></td>
            <td><?=$register->first_name.' '.$register->last_name;?></td>
            <td><?=$register->cuando?></td>
			<td><?=$register->importe?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
