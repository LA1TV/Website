<h1 class="no-top-margin"><a href="<?=e($hyperlink);?>" target="_blank"><?=e($episodeTitle);?></a></h1>
<div class="player-container embedded-player-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>" data-register-like-uri="<?=e($registerLikeUri);?>">
	<div class="embed-responsive embed-responsive-16by9">
		<div class="embed-responsive-item loading-container">
			<div class="msg">Player Loading</div>
		</div>
	</div>
</div>