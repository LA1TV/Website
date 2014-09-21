<?php if (!is_null($coverImageUri)): ?>
<div class="cover-image-container">
	<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
</div>
<?php endif; ?>
<div class="title-container clearfix">
<?php if (!is_null($seriesUri)): ?>
	<a class="view-series-button btn btn-info" href="<?=e($seriesUri);?>">View All Series</a>
<?php endif; ?>
	<h1 class="no-top-margin"><?=e($playlistTitle);?></h1>
</div>
<?php if (!is_null($escapedPlaylistDescription)): ?>
<p class="description"><?=$escapedPlaylistDescription?></p>
<?php endif; ?>
<?php if (is_null($playlistTableFragment)): ?>
<p class="coming-soon well well-sm">Coming soon!</p>
<?php else: ?>
<div class="playlist">
	<?=$playlistTableFragment?>
</div>
<?php if (!is_null($relatedItemsTableFragment)): ?>
	<div class="related-items">
		<?=$relatedItemsTableFragment?>
	</div>
<?php endif; ?>
<?php endif; ?>
