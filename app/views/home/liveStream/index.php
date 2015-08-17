<h1 class="no-top-margin"><?=e($title);?></h1>
<?php if (!is_null($descriptionEscaped)): ?>
<p><?=$descriptionEscaped?></p>
<?php endif; ?>
<div class="player-container-component-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-watching-uri="<?=e($registerWatchingUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>">
	<div class="msg-container">
		<div class="embed-responsive embed-responsive-16by9">
			<div class="embed-responsive-item">
				<div class="msg msg-loading">Loading<br /><img src="<?=asset("assets/img/loading.gif");?>"></div>
			</div>
		</div>
	</div>
</div>