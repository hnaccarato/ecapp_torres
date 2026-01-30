<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Create - users</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>admin/users_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12">
       		<form method="POST" name="users_form" id="users_form">
       		<div class="row">
       		    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="ip_address_id"><?=ucfirst('ip_address');?></label>
	        <input type="text" class="form-control" id="ip_address_id" placeholder="" name="ip_address">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="edificio_id_id"><?=ucfirst('edificio_id');?></label>
	        <input type="text" class="form-control" id="edificio_id_id" placeholder="" name="edificio_id">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="username_id"><?=ucfirst('username');?></label>
	        <input type="text" class="form-control" id="username_id" placeholder="" name="username">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="password_id"><?=ucfirst('password');?></label>
	        <input type="text" class="form-control" id="password_id" placeholder="" name="password">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="email_id"><?=ucfirst('email');?></label>
	        <input type="text" class="form-control" id="email_id" placeholder="" name="email">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="activation_code_id"><?=ucfirst('activation_code');?></label>
	        <input type="text" class="form-control" id="activation_code_id" placeholder="" name="activation_code">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="forgotten_password_code_id"><?=ucfirst('forgotten_password_code');?></label>
	        <input type="text" class="form-control" id="forgotten_password_code_id" placeholder="" name="forgotten_password_code">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="forgotten_password_time_id"><?=ucfirst('forgotten_password_time');?></label>
	        <input type="text" class="form-control" id="forgotten_password_time_id" placeholder="" name="forgotten_password_time">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="remember_code_id"><?=ucfirst('remember_code');?></label>
	        <input type="text" class="form-control" id="remember_code_id" placeholder="" name="remember_code">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="created_on_id"><?=ucfirst('created_on');?></label>
	        <input type="text" class="form-control" id="created_on_id" placeholder="" name="created_on">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="last_login_id"><?=ucfirst('last_login');?></label>
	        <input type="text" class="form-control" id="last_login_id" placeholder="" name="last_login">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="active_id"><?=ucfirst('active');?></label>
	        <input type="text" class="form-control" id="active_id" placeholder="" name="active">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="first_name_id"><?=ucfirst('first_name');?></label>
	        <input type="text" class="form-control" id="first_name_id" placeholder="" name="first_name">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="last_name_id"><?=ucfirst('last_name');?></label>
	        <input type="text" class="form-control" id="last_name_id" placeholder="" name="last_name">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="unidad_id"><?=ucfirst('unidad');?></label>
	        <input type="text" class="form-control" id="unidad_id" placeholder="" name="unidad">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="phone_id"><?=ucfirst('phone');?></label>
	        <input type="text" class="form-control" id="phone_id" placeholder="" name="phone">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="form-group">
	        <label for="alternative_phone_id"><?=ucfirst('alternative_phone');?></label>
	        <input type="text" class="form-control" id="alternative_phone_id" placeholder="" name="alternative_phone">
	    </div>
    </div>
    <div class="col-xs-12 col-sm-6">
    	<label for="timestamp_id"><?=ucfirst('timestamp');?></label>
    	<div class="input-group date">
		  <input type="text" class="form-control datepicker"  id="timestamp_id" placeholder="timestamp" name="timestamp"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
		</div>
    </div>

       		</div>
       		<button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>

       		</form>
        </div>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function (){
    $('.datepicker').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });
  })
</script>