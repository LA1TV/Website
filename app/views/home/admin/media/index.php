<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Media</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getSearchBar(); ?>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Enabled</th>
					<th>Name</th>
					<th>Description</th>
					<th>Has VOD</th>
					<th>Has Live Stream</th>
					<th>Live Stream State</th>
					<th>Time Created</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($tableData as $a): ?>
				<tr>
					<td><span class="<?=e($a['enabledCss']);?>"><?=e($a['enabled']);?></span></td>
					<td><?=e($a['name']);?></td>
					<td><?=e($a['description']);?></td>
					<td><span class="<?=e($a['hasVodCss']);?>"><?=e($a['hasVod']);?></span></td>
					<td><span class="<?=e($a['hasStreamCss']);?>"><?=e($a['hasStream']);?></span></td>
					<td><?=e($a['streamState']);?></td>
					<td><?=e($a['timeCreated']);?></td>
					<td class="action-col"><a class="btn btn-xs btn-info" href="<?=e($a['editUri'])?>">Edit</a> <button type="button" class="btn btn-xs btn-danger" data-action="delete" data-deleteuri="<?=e($deleteUri)?>" data-deleteid="<?=e($a['id'])?>">&times;</button></td>
				</tr>
			<?php endforeach; ?>
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
			</tbody>
		</table>
		<?= FormHelpers::getFormPageSelectionBar($pageNo, $noPages); ?>
	</div>
</div>