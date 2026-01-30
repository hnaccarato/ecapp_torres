<table class="table mc-read-table" id="table-read">
    <thead>
        <tr>
            <td data-column="view_circular.id"><?=ucfirst('n°');?></td>
            <td data-column="users.first_name"><?=ucfirst('Nombre');?></td>
            <td data-column="users.last_name"><?=ucfirst('Apellido');?></td>
            <td data-column="users.email"><?=ucfirst('Email');?></td>
            <td data-column="unidades.name"><?=ucfirst('Unidad');?></td>
            <td data-column="unidades.departamento"><?=ucfirst('Departamento');?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registers->result() as $register){ ?>
            <tr  id="<?=$register->id;?>">
                <td><?=$register->id;?></td>
                <td><?=$register->first_name;?></td>
                <td><?=$register->last_name;?></td>
                <td><?=$register->email;?></td>
                <td><?=$register->name;?></td>
                <td><?=$register->departamento;?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
