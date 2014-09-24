<?php if (count($promotedItemsData) > 0): ?>
<div class="promo-carousel">
	<?php foreach($promotedItemsData as $a): ?>
	<div class="item">
		<div class="embed-responsive embed-responsive-16by9">
			<div class="embed-responsive-item">
				<div class="content-container" data-jslink="<?=e($a['uri']);?>">
					<img src="<?=e($a['coverArtUri']);?>" class="cover-img img-responsive">
					<div class="footer">
						<div class="title"><?=e($a['name']);?></div>
						<div class="available-msg"><?=e($a['availableMsg']);?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row">
	<div class="most-popular-section col-md-6">
		<h2 class="heading">Most Popular</h2>
		<?php if (!is_null($mostPopularPlaylistFragment)): ?>
		<?=$mostPopularPlaylistFragment?>
		<?php else: ?>
		<div class="none-available">None available at the moment. Check back later!</div>
		<?php endif; ?>
	</div>
	<div class="recently-added-section col-md-6">
		<h2 class="heading">Recently Added</h2>
		<?php if (!is_null($recentlyAddedPlaylistFragment)): ?>
		<?=$recentlyAddedPlaylistFragment?>
		<?php else: ?>
		<div class="none-available">None available at the moment. Check back later!</div>
		<?php endif; ?>
	</div>
</div>