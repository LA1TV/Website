<?php if (!is_null($coverImageUri)): ?>
<div class="cover-image-container">
	<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
</div>
<?php endif; ?>
<h1 class="no-top-margin"><?=e($playlistTitle);?></h1>
<?php if (!is_null($playlistDescription)): ?>
<p class="description"><?=e($playlistDescription);?></p>
<?php endif; ?>
<?php if (is_null($playlistTableFragment)): ?>
<p class="coming-soon well well-sm">Coming soon!</p>
<?php else: ?>
<?=$playlistTableFragment?>
<?php endif; ?>
