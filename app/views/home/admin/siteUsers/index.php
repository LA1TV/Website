<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Site Users</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getSearchBar(); ?>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Banned</th>
					<th>Time Joined</th>
					<?php if ($editEnabled): ?>
					<th></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php foreach($tableData as $a): ?>
				<tr>
					<td><?=e($a['name']);?></td>
					<td><span class="<?=e($a['bannedCss']);?>"><?=e($a['banned']);?></span></td>
					<td><?=e($a['timeCreated']);?></td>
					<?php if ($editEnabled): ?>
					<td class="action-col"><a class="btn btn-xs btn-info" href="<?=e($a['editUri'])?>">Edit</a> <button type="button" class="btn btn-xs btn-danger" data-action="delete" data-deleteuri="<?=e($deleteUri)?>" data-deleteid="<?=e($a['id'])?>">&times;</button></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?= FormHelpers::getFormPageSelectionBar($pageNo, $noPages); ?>
	</div>
</div>