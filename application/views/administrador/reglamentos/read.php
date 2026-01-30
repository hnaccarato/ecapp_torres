<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
                    <td data-column="reglamentos.name"><?=ucfirst('tITULO');?></td>
                    <td data-column="reglamentos.file"><?=ucfirst('archivo');?></td>

            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
			<td><?=$register->titulo;?></td>
			<td class="text-left">
            <?php if(!empty($register->file)){ ?>
                <p>
                    <a href="<?=base_url('/upload/reglamentos/'.$register->file)?>" target="_blank" class="glyphicon glyphicon-save">Descargar</a>
                </p>
            <?php } ?>
            </td>
            <td>
                <ul class="mc-actions-list">
                    <li><a href="<?=base_url();?>administrador/reglamentos_update/<?=$register->id;?>" 
                        title="Editar Reglamento"><i class="fa fa-pencil"></i></a></li>
                    <li><a href="<?=base_url();?>administrador/reglamentos_delete/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="delete" title="Eliminar Reglamento"><i class="fa fa-times"></i></a></li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
