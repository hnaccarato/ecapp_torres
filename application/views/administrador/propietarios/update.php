<style type="text/css">
    .ms-list{
        margin: revert !important;
    }

</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Edit - Propietario</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/propietarios_list">
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
                        <label><?=lang('create_user_unidad_label', 'unidad');?></label>
                        <select class="form-control" name="unidad_id[]" id="unidad_id" multiple="multiple" required>
                            <?php foreach ($unidades->result() as $value) {?>
                                <option value="<?=$value->id?>"><?=$value->name?></option>  
                            <?php } ?>                            
                            <?php foreach ($my_unidades->result() as $value) {?>
                                <option value="<?=$value->unidad_id?>" 
                                    selected="selected"><?=$value->unidad_name?></option>  
                            <?php } ?>
                        </select>
                    </div>
                </div>  

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="email_id"><?=ucfirst('email');?></label>
                            <input type="text" class="form-control" id="email_id" placeholder="email" value="<?=$values->email;?>" disabled="disabled">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="email_id"><?=ucfirst('Email de Notificacion');?></label>
                            <input name="email_fw" type="mail" class="form-control" id="email_id" placeholder="Email de Notificacion" value="<?=$values->email_fw;?>" >
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
                        <div class="form-group">
                            <label for="alternative_phone_id"><?=ucfirst('alternative_phone');?></label>
                            <input type="text" class="form-control" id="alternative_phone_id" placeholder="alternative_phone" name="alternative_phone" value="<?=$values->alternative_phone;?>">
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                          <div class="panel-heading">Espacios Bloqueados</div>
                          <div class="panel-body">
                            <select name="baned[]" id="baned" multiple="multiple">
                                <?php foreach ($espacios as $value) {?>
                                    <option value="<?=$value->id?>"><?=$value->nombre_espacio?></option>  
                                <?php } ?>                            
                                <?php foreach ($baneos->result() as $value) {?>
                                    <option value="<?=$value->espacio_id?>" 
                                        selected="selected"><?=$value->espacio?></option>  
                                <?php } ?>
                            </select>
                          </div>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-mk-primary pull-left">Guardar</button>

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

        $('#unidad_id').multiSelect({
          selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Unidad N°'>",
          selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Unidad Activa'>",
          afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
            .on('keydown', function(e){
              if (e.which === 40){
                that.$selectableUl.focus();
                return false;
              }
            });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
            .on('keydown', function(e){
              if (e.which == 40){
                that.$selectionUl.focus();
                return false;
              }
            });
          },
          afterSelect: function(){
            this.qs1.cache();
            this.qs2.cache();
          },
          afterDeselect: function(){
            this.qs1.cache();
            this.qs2.cache();
          }
        });

        $('#baned').multiSelect({
          selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Espacio Nombre'>",
          selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Bloqueados'>",
          afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
            .on('keydown', function(e){
              if (e.which === 40){
                that.$selectableUl.focus();
                return false;
              }
            });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
            .on('keydown', function(e){
              if (e.which == 40){
                that.$selectionUl.focus();
                return false;
              }
            });
          },
          afterSelect: function(){
            this.qs1.cache();
            this.qs2.cache();
          },
          afterDeselect: function(){
            this.qs1.cache();
            this.qs2.cache();
          }
        });
    })
</script>