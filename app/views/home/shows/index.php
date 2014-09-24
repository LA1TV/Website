<h1 class="no-top-margin">Shows</h1>
<?php if (!is_null($playlistFragment)): ?>
<?=$playlistFragment?>
<?php else: ?>
<p class="no-items well well-sm">There are no shows at the moment.</p>
<?php endif; ?>
<div class="bottom-row clearfix">
	<?=$pageSelectorFragment?>
</div>