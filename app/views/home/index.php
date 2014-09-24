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