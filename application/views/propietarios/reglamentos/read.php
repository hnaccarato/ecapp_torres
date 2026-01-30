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
			<td>

            </td>
            <td>
            <?php if(!empty($register->file)){ ?>
                <p class="text-center">
                    <a href="<?=base_url('/upload/reglamentos/'.$register->file)?>" target="_blank" class="glyphicon glyphicon-save" title="Descragar Reglamento">Descarga</a>
                </p>
            <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
