<h1 class="no-top-margin">Live Guide <small>(<?=e($startDateStr);?> - <?=e($endDateStr);?>)</small></h1>

<table class="guide-table table table-bordered">
	<tbody>
<?php foreach($calendarData as $a): ?>
		<tr>
			<td class="col-date"><?=e($a['dateStr']);?></td>
			<td class="col-item"><?=$a['playlistFragment']?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>