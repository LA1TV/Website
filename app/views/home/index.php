<?php if (count($promotedItemsData) > 0): ?>
<div class="promo-carousel">
	<?php foreach($promotedItemsData as $a): ?>
	<div class="item">
		<div class="embed-responsive embed-responsive-16by9">
			<div class="content-container" data-jslink="<?=e($a['uri']);?>">
				<div class="bg">
					<img src="<?=e($a['coverArtUri']);?>" class="cover-img img-responsive">
				</div>
				<div class="footer">
					<div class="title"><?=e($a['name']);?></div>
					<?php if (!is_null($a['seriesName'])): ?>
					<div class="series-title"><?=e($a['seriesName']);?></div>
					<?php endif; ?>
					<div class="available-msg"><?=e($a['availableMsg']);?></div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row">
	<?php if (!is_null($twitterWidgetId)): ?>
	<div class="col-md-6">
	<?php endif; ?>
		<div class="most-popular-section<?=is_null($twitterWidgetId) ? " col-md-6" : ""?>">
			<h2 class="heading">Most Popular</h2>
			<?php if (!is_null($mostPopularPlaylistFragment)): ?>
			<?=$mostPopularPlaylistFragment?>
			<?php else: ?>
			<div class="none-available">None available at the moment. Check back later!</div>
			<?php endif; ?>
		</div>
		<div class="recently-added-section<?=is_null($twitterWidgetId) ? " col-md-6" : ""?>">
			<h2 class="heading">Recently Added</h2>
			<?php if (!is_null($recentlyAddedPlaylistFragment)): ?>
			<?=$recentlyAddedPlaylistFragment?>
			<?php else: ?>
			<div class="none-available">None available at the moment. Check back later!</div>
			<?php endif; ?>
		</div>
	<?php if (!is_null($twitterWidgetId)): ?>
	</div>
	<?php endif; ?>
	<?php if (!is_null($twitterWidgetId)): ?>
	<div class="col-md-6">
		<div class="twitter-timeline-container" data-twitter-widget-height="650" data-twitter-widget-id="<?=e($twitterWidgetId);?>"></div>
	</div>
	<?php endif; ?>
</div>