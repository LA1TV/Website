<div class="heading-container clearfix">
<?php if ($showHeading): ?>
	<div class="logo-container">
		<?php if (!$hasVideo): ?>
		<a href="<?=e($hyperlink);?>" target="_blank"><img class="img-responsive" src="<?=asset("assets/img/logo.png");?>"/></a>
		<?php endif; ?>
	</div>
	<div class="title-container">
		<?php if ($hasVideo): ?>
		<h1 class="episode-title"><a href="<?=e($hyperlink);?>" target="_blank"><?=e($episodeTitle);?></a></h1>
		<?php else: ?>
		<h1 class="episode-title">Content Unavailable</h1>
		<?php endif; ?>
	</div>
<?php endif; ?>
</div>
<?php if ($hasVideo): ?>
<div class="player-container-component-container" data-site-uri="<?=e($hyperlink);?>" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-update-playback-time-base-uri="<?=e($updatePlaybackTimeBaseUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>" data-register-like-uri="<?=e($registerLikeUri);?>" data-ignore-external-stream-url="<?=$ignoreExternalStreamUrl?"1":"0"?>" data-hide-bottom-bar="<?=$hideBottomBar?"1":"0"?>" data-auto-play-vod="<?=$autoPlayVod?"1":"0"?>" data-auto-play-stream="<?=$autoPlayStream?"1":"0"?>" data-disable-full-screen="<?=$disableFullScreen?"1":"0"?>" data-initial-vod-quality-id="<?=$initialVodQualityId?>" data-initial-stream-quality-id="<?=$initialStreamQualityId?>">
<?php else: ?>
<div class="player-container-component-container" data-site-uri="<?=e($hyperlink);?>">
<?php endif; ?>
	<div class="msg-container embed-responsive">
		<div class="embed-responsive-item">
			<?php if ($hasVideo): ?>
			<div class="msg msg-loading">Loading<br /><img src="<?=asset("assets/img/loading.gif");?>"></div>
			<?php else: ?>
			<div class="msg msg-unavailable">Sorry this content is currently unavailable.<br /><a href="<?=e($hyperlink);?>" target="_blank">Click here to go to the LA1:TV website.</a></div>
			<?php endif; ?>
		</div>
	</div>
</div>