<script type="text/javascript">
    $(document).ready(function (){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

        $('#unidad_id').multiSelect({
          selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"12\"'>",
          selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"4\"'>",
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

<?php $attrib = array('class' => 'form-control');?>
<?php $attrib_submit = array('class' => 'btn btn-mk-primary');?>

<?=form_open_multipart();?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Perfil</h2>
            <p><?=lang('create_user_subheading');?></p>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url('propietarios');?>"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
       <hr class="hr_subrayado">
   <div class="row">
        <div class="col-xs-12">
            <div id="infoMessage"><?=$message;?></div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Usaurio De Acceso</label>
                <?=form_input($email, '', $attrib);?>
            </div>
        </div>
        <div class="row">
             <div class="col-xs-6">
                 <div class="form-group">
                     <label for="alternative_phone_id"><?=ucfirst('Foto de perfil');?></label>
                     <br>
                     <?php if(is_file(BASEPATH.'../upload/profile/'.$photo)){?>
                         <img src="<?=base_url('upload/profile/'.$photo);?>"class="img-thumbnail">
                     <?php } ?>
                 </div>
             </div>                    
             <div class="col-xs-6">
                 <div class="form-group">
                     <label for="alternative_phone_id"><?=ucfirst('Su foto');?></label>
                     <input type="file" name="photo" class="form-control" id="alternative_phone_id" accept="image/*">
                 </div>
             </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=ucfirst('Email de Notificacion');?></label>
                <?=form_input($email_fw, '', $attrib);?>
            </div>
        </div>        
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=lang('create_user_fname_label', 'first_name');?></label>
                <?=form_input($first_name, '', $attrib);?>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=lang('create_user_lname_label', 'last_name');?></label>
                <?=form_input($last_name, '', $attrib);?>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=lang('create_user_phone_label', 'phone');?></label>
                <?=form_input($phone, '', $attrib);?>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=lang('create_user_password_label', 'password');?></label>
                <?=form_input($password, '', $attrib);?>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?=lang('create_user_password_confirm_label', 'password_confirm');?></label>
                <?=form_input($password_confirm, '', $attrib);?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <?=form_submit('submit','Guardar', $attrib_submit);?>
                
            </div>
        </div>
    </div>
</div>
<?=form_close();?>
<script type="text/javascript">
    $(document).ready(function (){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });

        $('#unidad_id').multiSelect({
          selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"12\"'>",
          selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"4\"'>",
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