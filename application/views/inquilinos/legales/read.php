<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="seguros.name"><?=ucfirst('Titulo');?></td>
            <?php if ($legal_id == JUCIOS): ?>
                <td data-column="seguros.file"><?=ucfirst('Abogado');?></td>
                <td data-column="seguros.file"><?=ucfirst('Legajo');?></td>
            <?php endif; ?>
            <td data-column="seguros.file"><?=ucfirst('archivo');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
			<td><?=$register->titulo;?></td>
           
            <?php if ($legal_id == JUCIOS): ?>
                <td><?=$register->abogado;?></td>
                <td><?=$register->legajo;?></td>
             <?php endif; ?>

			<td>
            <?php  
                $rs = $this->Legales_model->my_files($register->id); 
                if($rs):
            ?>   
               
                <table width="100%">
                   
                    <?php foreach ($rs->result() as $value) { ?>
                        <tr>
                            <td> <?=$value->name?> :</td> 
                            <td>
                                <a href="<?=base_url('/upload/legales/'.$value->file)?>" target="_blank" class="glyphicon glyphicon-save"> DESCARGAR</a> 
                            </td>
                        </tr>
                    <?php } ?>
                
                </table>
            <?php endif; ?>
            </td>
        </tr>

        <?php } ?>
    </tbody>
</table>
