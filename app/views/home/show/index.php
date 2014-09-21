<?php if (!is_null($coverImageUri)): ?>
<div class="cover-image-container">
	<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
</div>
<?php endif; ?>
<h1 class="no-top-margin"><?=e($showTitle);?></h1>
<?php if (!is_null($escapedShowDescription)): ?>
<p class="description"><?=$escapedShowDescription?></p>
<?php endif; ?>
<?php if (is_null($showTableFragment)): ?>
<p class="coming-soon well well-sm">Coming soon!</p>
<?php else: ?>
<div class="playlist">
	<?=$showTableFragment?>
</div>
<?php endif; ?>
