<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="recibos.id"><?=ucfirst('N°');?></td>
            <td data-column="recibos.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="recibos.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="recibos.descripcion"><?=ucfirst('Visible propietarios');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td align="center">EXP<?=$register->id;?></td>
            <td align="center">
                <?php setlocale(LC_ALL,"es_ES");?>
                <?=strftime("%B %Y",strtotime($register->fecha));?>
            </td>
            <td align="center"><?=$register->titulo;?></td>
            <td  align="center"><?=($register->estado_id == ENVIADO)? "Si":"No";?></td>
            <td align="center">
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>administrador/expensas_update/<?=$register->id;?>" title="Editar Expensa"><i class="fa fa-pencil"></i></a>
                    </li>
                  
                    <li>
                        <a href="#" data-id="<?=$register->id;?>" class="ebancarios"><i class="fa fa-file" aria-hidden="true" title="Agregar comprobantes"></i></a>
                    </li>                    
                    <li>
                        <a href="<?=base_url();?>administrador/expensas_view/<?=$register->id;?>" >
                            <i class="fa fa-eye" aria-hidden="true" title="Ver Expensas"></i>
                        </a>
                    </li>                    
                    <li>
                        <a href="#" data-id="<?=$register->id;?>" class="notificar">
                            <i class="fa fa-paper-plane-o" aria-hidden="true" title="Notificar Expensas"></i>
                        </a>
                    </li>    
                    <li>
                        <a href="<?=base_url();?>administrador/recibos_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Expensa">
                            <i class="fa fa-times"></i>
                        </a>
                    </li>    
                    <!--              
                    <li>
                        <a href="#" data-id="<?=$register->id;?>" class="newPago">
                            <i class="fa fa-money" aria-hidden="true"></i>
                        </a>
                    </li>
                    -->
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
