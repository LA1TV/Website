<h1 class="no-top-margin">Playlists</h1>

<?php if (!is_null($playlistFragment)): ?>
<?=$playlistFragment?>
<?php else: ?>
<p class="no-items well well-sm">There are no playlists at the moment.</p>
<?php endif; ?>
<?php if (!is_null($nextPageUri) || !is_null($previousPageUri)):?>
<div class="bottom-row clearfix">
	<div class="page-numbers-container">
		<ul class="pagination">
			<?php if (!is_null($previousPageUri)):?>
			<li><a href="<?=e($previousPageUri);?>">&laquo;</a></li>
			<?php else: ?>
			<li class="disabled"><a href="">&laquo;</a></li>
			<?php endif; ?>
			<?php foreach($pageNumbers as $a): ?>
			<li<?=$a['active']?' class="active"':""?>><a href="<?=e($a['uri']);?>"><?=e($a['num']);?></a></li>
			<?php endforeach; ?>
			<?php if (!is_null($nextPageUri)):?>
			<li><a href="<?=e($nextPageUri);?>">&raquo;</a></li>
			<?php else: ?>
			<li class="disabled"><a href="">&raquo;</a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php endif; ?>