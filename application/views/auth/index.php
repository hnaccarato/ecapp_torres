<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
           	<h2 class="mc-page-header"><?php echo lang('index_heading');?></h2>
            <p><?php echo lang('index_subheading');?></p>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li><a type="button" href="<?=base_url();?>auth/create_user" title="<?=lang('index_create_user_link');?>"><i class="fa fa-plus"></i></a></li>
            </ul>
        </div>
        <hr>
        <div class="col-xs-12">
        	<div id="infoMessage"><?php echo $message;?></div>
        </div>
        <div class="col-xs-12">
            <div class="mc-table-responsive" id="mc-table">
          
                <table id="table-read" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                    		<th><?php echo lang('index_fname_th');?></th>
                    		<th><?php echo lang('index_lname_th');?></th>
                    		<th><?php echo lang('index_email_th');?></th>
                    		<th><?php echo lang('index_groups_th');?></th>
                    		<th><?php echo lang('index_status_th');?></th>
                    		<th><?php echo lang('index_action_th');?></th>
                    	</tr>
                    </thead>
                    <tbody>
                	<?php foreach ($users as $user):?>
                		<tr>
                            <td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
                			<td>
                				<?php foreach ($user->groups as $group):?>
                					<?php echo anchor("auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
                                <?php endforeach?>
                			</td>
                			<td><?php echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('index_active_link')) : anchor("auth/activate/". $user->id, lang('index_inactive_link'));?></td>
                			<td>
                				<ul class="mc-actions-list">
                                    <li>
                                        <a href="<?=base_url();?>auth/edit_user/<?=$user->id;?>"><i class="fa fa-pencil"></i>
                                        </a>
                                    </li>                				    
                                    <li>
                                        <a href="<?=base_url();?>auth/delete_user/<?=$user->id;?>"><i class="fa fa-times"></i>
                                        </a>
                                    </li>
                				</ul>
                			</td>
                		
                		</tr>
                	<?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-read').DataTable();
    } );

/*    $(document).ready(function() {
        var table = $('#example').DataTable( {
            lengthChange: false,
            buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
        } );
     
        table.buttons().container()
            .appendTo( '#example_wrapper .col-sm-6:eq(0)' );
    } );*/
</script>

