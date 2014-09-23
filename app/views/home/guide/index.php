<h1 class="no-top-margin">Live Guide <small>(<?=e($titleDatesStr);?>)</small></h1>

<table class="guide-table table table-bordered table-hover">
	<tbody>
<?php if (count($calendarData) > 0): ?>
<?php foreach($calendarData as $a): ?>
		<tr>
			<td class="col-date"><?=e($a['dateStr']);?></td>
			<td class="col-item"><?=$a['playlistFragment']?></td>
		</tr>
<?php endforeach; ?>
<?php else: ?>
<p class="no-items well well-sm">There are no livestreams scheduled for this date range.</p>
<?php endif; ?>
	</tbody>
</table>
<?php if (!is_null($nextPageUri) || !is_null($previousPageUri)):?>
<div class="bottom-row clearfix">
	<div class="page-numbers-container">
		<ul class="pagination">
			<?php if (!is_null($previousPageUri)):?>
			<li><a href="<?=e($previousPageUri);?>">&laquo; <?=e($previousPageStartDateStr);?></a></li>
			<?php endif; ?>
			<?php if (!is_null($nextPageUri)):?>
			<li><a href="<?=e($nextPageUri);?>"><?=e($nextPageStartDateStr);?> &raquo;</a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php endif; ?>