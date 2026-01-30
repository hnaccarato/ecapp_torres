<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="users.first_name"><?=ucfirst('Nombre');?></td>
            <td data-column="users.last_name"><?=ucfirst('Apellido');?></td>
            <td data-column="users.email"><?=ucfirst('email');?></td>
            <td data-column="users.active"><?=ucfirst('Grupo');?></td>
            <td data-column="users.active"><?=ucfirst('active');?></td>
            <td>Acciones</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
        <tr  rel="<?=$register->id;?>">
			<td><?=$register->first_name;?></td>
			<td><?=$register->last_name;?></td>
            <td><?=$register->email;?></td>
            <td>
                <?php foreach ($this->ion_auth->get_users_groups($register->id)->result() as $value) {
                   echo htmlspecialchars($value->name,ENT_QUOTES,'UTF-8')." ";
                }?>
                
            </td>
            <td><?=$register->active;?></td>
            <td>

                <a href="<?=base_url();?>admin/users_update/<?=$register->id;?>"  class="btn btn-info btn-xs">
                    <span class="fa fa-pencil"></span> Edit</a> 
                <a href="<?=base_url();?>admin/users_delete/<?=$register->id;?>" class="btn btn-danger btn-xs">
                    <span class="fa fa-trash"></span> Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
