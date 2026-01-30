<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="espacios.nombre_espacio"><?=ucfirst('nombre espacio');?></td>
            <td data-column="reservas.dia_calendario"><?=ucfirst('fecha');?></td>
            <td data-column="reservas.hora_reserva"><?=ucfirst('desde');?></td>
            <td data-column="reservas.hora_hasta"><?=ucfirst('hasta');?></td>  
            <td data-column="reservas.timestamp"><?=ucfirst('importe');?></td>
            <td data-column="reservas.timestamp"><?=ucfirst('Acciones');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>" id="<?=$register->id;?>">
            <td><?=$register->nombre_espacio;?></td>
            <td><?=$register->date;?></td>
            <td><?=$register->desde;?></td>
            <td><?=$register->hasta;?></td>
            <td><?=$register->importe?></td>
            <td>
             
                <a class="btn btn-success btn-xs addinvitado" title="Agregar Invitados" href="<?=base_url('inquilinos/espacios_invitados/'.$register->id)?>"  target="_self" >
                    <i class="fa fa-plus"></i> <span class="glyphicon glyphicon-user"></span> Invitados
                </a>
                <a class="btn btn-danger btn-xs activation rechazado" title="Rechazar reserva" data-id="<?=$register->id?>" href="javascript:void(0)"  target="_self" >
                    <span class="fa fa-trash"></span> Borrar
                </a>   
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
