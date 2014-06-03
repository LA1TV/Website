<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Media</h3>
	</div>
	<div class="panel-body">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Has VOD</th>
					<th>Has Live Stream</th>
					<th>Time Created</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($tableData as $a): ?>
				<tr>
					<td><?=e($a['name']);?></td>
					<td><?=e($a['description']);?></td>
					<td><span class="<?=e($a['has_vod_css']);?>"><?=e($a['has_vod']);?></span></td>
					<td><span class="<?=e($a['has_stream_css']);?>"><?=e($a['has_stream']);?></span></td>
					<td><?=e($a['time_created']);?></td>
					<td class="action-col"><button type="button" class="btn btn-xs btn-info">Edit</button> <button type="button" class="btn btn-xs btn-danger">&times;</button></td>
				</tr>
			<?php endforeach; ?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="action-col"><button type="button" class="btn btn-xs btn-primary">Create</button></td>
				</tr>
			</tbody>
		</table>
		<?= FormHelpers::getFormPageSelectionBar($pageNo, $noPages); ?>
	</div>
</div>