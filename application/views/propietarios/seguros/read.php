<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
        <td data-column="seguros.name"><?=ucfirst('tITULO');?></td>
        <td data-column="seguros.file"><?=ucfirst('archivo');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
			<td><?=$register->titulo;?></td>
			<td>
            <?php if(!empty($register->file)){ ?>
                <p class="text-center">
                    <a href="<?=base_url('/upload/seguros/'.$register->file)?>" target="_blank" class="btn btn-info btn-xs""><i class="glyphicon glyphicon-save"></i>Descarga</a>
                </p>
            <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
