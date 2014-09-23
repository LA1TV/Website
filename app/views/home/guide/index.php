<h1 class="no-top-margin">Live Guide <small>(<?=e($titleDatesStr);?>)</small></h1>

<table class="guide-table table table-bordered table-hover">
	<tbody>
<?php foreach($calendarData as $a): ?>
		<tr>
			<td class="col-date"><?=e($a['dateStr']);?></td>
			<td class="col-item"><?=$a['playlistFragment']?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<div class="bottom-row clearfix">
	<div class="page-numbers-container">
		<ul class="pagination">
			<li><a href="#">&laquo; 23rd Sep</a></li>
			<li><a href="#">25th Sep &raquo;</a></li>
		</ul>
	</div>
</div>