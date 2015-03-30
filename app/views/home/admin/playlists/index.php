<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Playlists</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getSearchBar(); ?>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Enabled</th>
					<th>Show</th>
					<th>Name</th>
					<th>Description</th>
					<th>Number Of Items</th>
					<th>Custom URI</th>
					<th>Time Created</th>
					<?php if ($editEnabled): ?>
					<th class="action-col"><a type="button" class="btn btn-xs btn-primary" href="<?=e($createUri)?>">Create</a></th>
					<?php endif ?>
				</tr>
			</thead>
			<tbody>
			<?php foreach($tableData as $a): ?>
				<tr>
					<td><span class="<?=e($a['enabledCss']);?>"><?=e($a['enabled']);?></span></td>
					<td><?=e($a['show']);?></td>
					<td><?=e($a['name']);?></td>
					<td><?=e($a['description']);?></td>
					<td><?=e($a['noPlaylistItems']);?></td>
					<td><?=e($a['customUri']);?></td>
					<td><?=e($a['timeCreated']);?></td>
					<?php if ($editEnabled): ?>
					<td class="action-col"><a class="btn btn-xs btn-info" href="<?=e($a['editUri'])?>">Edit</a> <button type="button" class="btn btn-xs btn-danger" data-action="delete" data-deleteuri="<?=e($deleteUri)?>" data-deleteid="<?=e($a['id'])?>">&times;</button></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
				<?php if ($editEnabled): ?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="action-col"><a type="button" class="btn btn-xs btn-primary" href="<?=e($createUri)?>">Create</a></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?= FormHelpers::getFormPageSelectionBar($pageNo, $noPages); ?>
	</div>
</div>