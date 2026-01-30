<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="cartelera.edificio_id"><?=ucfirst('edificio');?></td>
            <td data-column="cartelera.titulo"><?=ucfirst('titulo');?></td>
            <td data-column="cartelera.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="cartelera.detalle"><?=ucfirst('detalle');?></td>
    
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>">
            <td><?=$register->edificio;?></td>
            <td><?=$register->titulo;?></td>
            <td><?=$register->fecha_envio;?></td>
            <td><?=substr($register->detalle, 0, 24);?>..</td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>inquilinos/view_circular/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" title="Ver Circular">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </td>

        </tr>
        <?php } ?>
    </tbody>
</table>
