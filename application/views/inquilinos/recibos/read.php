<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="recibos.fecha"><?=ucfirst('fecha');?></td>
            <td data-column="recibos.titulo"><?=ucfirst('titulo');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>">
            <td>
                <?php setlocale(LC_ALL,"es_ES");?>
                <?=strftime("%B %Y",strtotime($register->fecha));?>
            </td>
            <td><?=$register->titulo;?></td>
            <td>
                <ul class="mc-actions-list">
                    <li>
                        <a href="<?=base_url();?>inquilinos/expensas_view/<?=$register->id;?>" title="Ver Expensa" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
