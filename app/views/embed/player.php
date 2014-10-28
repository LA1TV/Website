<div class="heading-container clearfix">
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
</div>
<?php if ($hasVideo): ?>
<div class="player-container-component-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>" data-register-like-uri="<?=e($registerLikeUri);?>">
<?php else: ?>
<div class="player-container-component-container">
<?php endif; ?>
	<div class="msg-container embed-responsive">
		<div class="embed-responsive-item">
			<?php if ($hasVideo): ?>
			<div class="msg msg-loading">Player Loading</div>
			<?php else: ?>
			<div class="msg msg-unavailable">Sorry this content is currently unavailable.<br /><a href="<?=e($hyperlink);?>" target="_blank">Click here to go to the LA1:TV website.</a></div>
			<?php endif; ?>
		</div>
	</div>
</div>