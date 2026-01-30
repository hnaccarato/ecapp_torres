<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Edit - Propietario</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/seguridad_list">
                        <i class="fa fa-arrow-circle-o-left"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="hr_subrayado">
    <div class="row">      
        <div class="col-xs-12">
            <form method="POST" name="users_form" id="users_form">
                <div class="row">

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="email_id"><?=ucfirst('email');?></label>
                            <input type="text" class="form-control" id="email_id" placeholder="email" name="email" value="<?=$values->email;?>">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="first_name_id"><?=ucfirst('first_name');?></label>
                            <input type="text" class="form-control" id="first_name_id" placeholder="first_name" name="first_name" value="<?=$values->first_name;?>">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="last_name_id"><?=ucfirst('last_name');?></label>
                            <input type="text" class="form-control" id="last_name_id" placeholder="last_name" name="last_name" value="<?=$values->last_name;?>">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="phone_id"><?=ucfirst('phone');?></label>
                            <input type="text" class="form-control" id="phone_id" placeholder="phone" name="phone" value="<?=$values->phone;?>">
                        </div>
                    </div>


                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="password_id"><?=ucfirst('password');?></label>
                            <input type="text" class="form-control" id="password_id" placeholder="password" name="password" pattern=".{0}|.{8,}" title="minimo 8 caracteres" 
                            value="">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" id="add_reservation" name="deservation" value="1">
                          <label class="form-check-label" for="add_reservation">Puede reservar ?</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-mk-primary pull-right">Guardar</button>

            </form>

        </div>
    </div>
</div>  