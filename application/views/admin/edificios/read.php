<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="edificios.nombre"><?=ucfirst('nombre');?></td>
            <td data-column="edificios.direccion"><?=ucfirst('direccion');?></td>
            <td data-column="edificios.telefono"><?=ucfirst('Empresa');?></td>
            <td data-column="edificios.imagen"><?=ucfirst('Plan');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  id="<?=$register->id;?>">
            <td><?=$register->nombre;?></td>
            <td><?=$register->direccion;?></td>
            <td><?=$register->empresa;?></td>
            <td><?=$register->categoria;?></td>

            <td>
                <a href="<?=base_url();?>admin/edificios_update/<?=$register->id;?>"  class="btn btn-info btn-xs">
                    <span class="fa fa-pencil"></span> Edit</a> 
                <a href="<?=base_url();?>admin/edificios_delete/<?=$register->id;?>" class="btn btn-danger btn-xs delete" data-id="<?=$register->id;?>" >
                    <span class="fa fa-trash"></span> Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<div class="pull-left">
    <?php echo $this->pagination->create_links(); ?>                    
</div>     
