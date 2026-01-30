<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
           	<h2 class="mc-page-header">Grupos</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li><a type="button" href="<?=base_url();?>auth/create_group" title="<?=lang('index_create_user_link');?>"><i class="fa fa-plus"></i></a></li>
            </ul>
        </div>
        <div class="col-xs-12">
        	<div id="infoMessage"></div>
        </div>
        <div class="col-xs-12">
            <div class="mc-table-responsive" id="mc-table">
                <table class="table mc-read-table" id="table-read">
                	<tr>
                		<th>Id</th>
                		<th>Name</th>
                		<th>Description</th>
                		<th>Action</th>
                	</tr>
                	<?php foreach ($groups as $group): ?>
                		<tr>
                			<td><?php echo htmlspecialchars($group->id,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo htmlspecialchars($group->name,ENT_QUOTES,'UTF-8');?></td>
                            <td><?php echo htmlspecialchars($group->description,ENT_QUOTES,'UTF-8');?></td>
                			
                			<td>
                				<ul class="mc-actions-list">
                				    <li><a href="<?=base_url();?>auth/edit_group/<?=$group->id;?>"><i class="fa fa-pencil"></i></a></li>
                				</ul>
                			</td>
                		
                		</tr>
                	<?php endforeach;?>
                </table>
            </div>
        </div>
    </div>
</div>

