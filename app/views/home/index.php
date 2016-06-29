<?php
	$twoColumns = !is_null($twitterWidgetId) || $showFacebookWidget;
?>
<div class="wrapper hidden">
	<?php if ($showPromoItem): ?>
	<div class="promo-item-container">
		<div class="player-container-component-container" data-info-uri="<?=e($promoPlayerInfoUri);?>" data-register-watching-uri="<?=e($promoRegisterWatchingUri);?>" data-register-like-uri="<?=e($promoRegisterLikeUri);?>" data-login-required-msg="<?=e($promoLoginRequiredMsg);?>" data-enable-admin-override="<?=$promoAdminOverrideEnabled?"1":"0"?>"></div>
	</div>
	<?php endif; ?>
	<?php if (!$showPromoItem && count($promotedItemsData) > 0): ?>
	<div class="promo-carousel flexslider">
		<ul class="slides">
			<?php foreach($promotedItemsData as $a): ?>
			<li class="item">
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
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<div class="row">
		<?php if ($twoColumns): ?>
		<div class="col-md-7">
		<?php endif; ?>
			<?php if ($twoColumns): ?>
			<div class="list-selection-button-group"></div>	
				<div class="lists animation-enabled" data-list="mostPopular">
					<div class="carousel">
			<?php else: ?>
			<div class="lists">
			<?php endif; ?>	
					<div class="most-popular-section<?=is_null($twitterWidgetId) ? " col-md-6" : ""?>">
						<?php if (!$twoColumns): ?>
						<h2 class="heading">Most Popular</h2>
						<?php endif; ?>
						<div class="list-holder">
							<?php if (!is_null($mostPopularPlaylistFragment)): ?>
							<?=$mostPopularPlaylistFragment?>
							<?php else: ?>
							<div class="none-available">None available at the moment. Check back later!</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="recently-added-section<?=is_null($twitterWidgetId) ? " col-md-6" : ""?>">
						<?php if (!$twoColumns): ?>
						<h2 class="heading">Recently Added</h2>
						<div class="list-holder">
						<?php else: ?>
						<div class="list-holder hidden">
						<?php endif; ?>
							<?php if (!is_null($recentlyAddedPlaylistFragment)): ?>
							<?=$recentlyAddedPlaylistFragment?>
							<?php else: ?>
							<div class="none-available">None available at the moment. Check back later!</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php if ($twoColumns): ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if ($twoColumns): ?>
		<div class="col-md-5">
			<?php if (!is_null($showFacebookWidget)): ?>
			<div class="facebook-timeline-container" data-show-messages="0" data-page-url="<?=e($facebookPageUrl);?>" data-height="620"></div>
			<?php endif; ?>
			<?php if (!is_null($twitterWidgetId)): ?>
			<div class="twitter-timeline-container" data-twitter-widget-height="620" data-twitter-widget-id="<?=e($twitterWidgetId);?>"></div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>