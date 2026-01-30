<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="edificios.nombre"><?=ucfirst('Edificio');?></td>
            <td data-column="users.first_name"><?=ucfirst('Usuario');?></td>
            <td data-column="propuestas.fecha_fin"><?=ucfirst('fecha fin');?></td>
            <td data-column="propuestas.sector"><?=ucfirst('Titulo');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>">
			<td><?=$register->edificio;?></td>
			<td><?=$register->nombre;?> <?=$register->apellido;?></td>
			<td><?=$register->fecha_fin;?></td>
			<td><?=$register->titulo;?></td>
            <td>
                
               <?php if($this->votaciones_model->i_voted($register->id)):?>
                    <a href="<?=base_url();?>propietarios/propuestas_view/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-info btn-xs">
                        <i class="glyphicon glyphicon-eye-open" aria-hidden="true" title="Ver Votación"></i> ver  
                    </a>
                <?php else: ?>
                    <a href="<?=base_url();?>propietarios/propuestas_view/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-success btn-xs">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true" title="Votar"></i> Votar  
                    </a>
                <?php endif; ?>  
          
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
