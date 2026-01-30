<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="espacios.nombre_espacio"><?=ucfirst('Espacio');?></td>
            <td data-column="reservas.dia_calendario"><?=ucfirst('fecha');?></td>
            <td data-column="reservas.hora_reserva"><?=ucfirst('desde');?></td>
            <td data-column="reservas.hora_hasta"><?=ucfirst('hasta');?></td>  
            <td data-column="espacios.max"><?=ucfirst('unidad');?></td>
            <td data-column="espacios.max"><?=ucfirst('Departamento');?></td>
            <td data-column="users.first_name"><?=ucfirst('Asistentes');?></td>
            <td data-column="users.first_name"><?=ucfirst('Propietario');?></td>
            <td data-column="reservas.timestamp"><?=ucfirst('importe');?></td>
            <?php if(!$is_rechasado){ ?>
                <td data-column="reservas.timestamp"><?=ucfirst('Estado');?></td>
                <td data-column="reservas.timestamp"><?=ucfirst('Acciones');?></td>
            <?php }else{ ?> 
                <td data-column="reservas.timestamp"><?=ucfirst('Cuando');?></td>
            <?php } ?>

        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>" id="<?=$register->id;?>">
            <td><?=$register->nombre_espacio;?></td>
            <td><?=$register->date;?></td>
            <td><?=$register->desde;?></td>
			<td><?=$register->hasta;?></td>
            <td><?=$register->unidad;?></td>
			<td><?=$register->departamento;?></td>
            <td><?=$register->total_ivitados;?></td>
            <td>
                <a href="<?=base_url('administrador/consultas_create/'.$register->user_id)?>">
                    <?=$register->first_name.' '.$register->last_name;?>        
                </a>
            </td>
            <td><?=$register->importe?></td>
			<?php if(!$is_rechasado){ ?>
            <td>
            <?php if($register->estado_id == PENDIENTE){ ?> 
                <a class="btn btn-success btn-xs activation aprobado" title="Aprobar Reservas" href="" 
                data-id="<?=$register->id?>" target="_self" rel="<?=APROBADO?>">
                <span class="glyphicon glyphicon-ok"></span> 
                </a>
            <?php } ?>
                <a class="btn btn-danger btn-xs activation rechazado" title="Rechazar Reservas" href="" 
                data-id="<?=$register->id?>" target="_self" rel="<?=RECHAZADO?>" >
                 <span class="glyphicon glyphicon-remove"></span> 
                </a>             
            </td>
            <td>     
                <a class="btn btn-success btn-xs addinvitado" title="Agregar Invitados" href="<?=base_url('administrador/espacios_invitados/'.$register->id)?>"  target="_self" >
                    <i class="fa fa-plus"></i> <span class="glyphicon glyphicon-user"></span> 
                </a>

            </td>
            <?php  }else {?>
                <td><?=$register->cuando;?></td>
            <?php } ?>
        </tr>
        <?php } ?> 
    </tbody>
</table>

<script type="text/javascript">
    
      $(document).ready(function(){

        $("body").on('click','.activation',function(e){ 
            e.preventDefault();

            var input = $(this);
            var url = "<?=base_url('administrador/espacios_active')?>"; 
            var reserva_id = $(this).data('id');
            var estado_id = $(this).attr('rel');

            if($(this).hasClass('aprobado')){
                var text = "Desea confirmar la reserva ?";
                var text_success = "Reserva Confirmada";
            }    

            if($(this).hasClass('rechazado')){
                var text = "Desea rechazar la reserva ?";
                var text_success = "Reserva rechazada!";
            }

            Swal.fire({
                title: text,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3498DB',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
        
                    $.post(url,{estado_id:estado_id,reserva_id:reserva_id}, 
                        function(data){
                            var data = JSON.parse(data);
                            var alert_icono = 'success';
                            
                            if(data.error.length > 0){
                                text_success =  data.error;
                                alert_icono = 'warning';
                            }

                            Swal.fire(
                                text_success,
                                '',
                                alert_icono
                                );
                            
                            if(data.error.length == 0){
                                $("#"+reserva_id).remove();
                            }

                        }
                    );
                }
            })
        });

    });
</script>
<style type="text/css">
    .btn{
        color: #FFF !important;
    }
</style>