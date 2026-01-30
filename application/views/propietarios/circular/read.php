<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="circular.edificio_id"><?=ucfirst('edificio');?></td>
            <td data-column="circular.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="circular.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="circular.detalle"><?=ucfirst('detalle');?></td>
    
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr>
            <td><?=$register->edificio;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=$register->fecha_envio;?></td>
            <td><?=substr($register->detalle, 0, 24);?>..</td>
            <td>
                    <a href="<?=base_url();?>propietarios/view_circular/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-info btn-xs" title="Ver Circular">
                        <i class="fa fa-eye" aria-hidden="true"></i> ver
                    </a>
            </td>

        </tr>
        <?php } ?>
    </tbody>
</table>
