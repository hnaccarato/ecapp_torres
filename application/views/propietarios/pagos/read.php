<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="pagos_users.id"><?=ucfirst('N°');?></td>
            <td data-column="recibos.titulo"><?=ucfirst('Expensas');?></td>
            <td data-column="recibos.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="users.unidad"><?=ucfirst('Unidad');?></td>
            <td data-column="users.nombre"><?=ucfirst('Nombre');?></td>
            <td data-column="pagos_users.estado_id"><?=ucfirst('Estado');?></td>
            <td data-column="pagos_users.file"><?=ucfirst('Comprobante / MP id');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>">
            <td><?=$register->id;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=date('d/m/Y',strtotime($register->fecha_pago));?></td>
            <td><?=$register->unidad;?></td>
            <td><?=$register->nombre;?> - <?=$register->apellido;?></td>
            <td><samp class="estado_<?=$register->estado_id?>"><?=$register->estado;?></samp></td>
            <td class="text-center">
                <?php if (empty($register->collection_id)) { ?>
                    
                    <a  href="<?=base_url("upload/comprobante/".$register->comprobante)?>" 
                        target="_blank" id="expensa_file">
                        Comprobante <span class="glyphicon glyphicon-save-file"></span>
                    </a>

                <?php }else{ ?> 

                    <strong><?=$register->collection_id;?></strong>

                <?php } ?>
            </td>
            <td>
                <a class="view_expensa btn btn-info btn-xs" data-primary-key="<?=$register->id;?>"  
                    href="javascript:void(0)" title="Ver Comprobante de pago"><i class="fa fa-eye" aria-hidden="true"></i> Ver</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
