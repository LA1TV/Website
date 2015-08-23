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

<div class="row schedule-boxes" data-schedule-uri="<?=e($scheduleUri);?>">
	<div class="col-md-4 schedule-box-prev-live-container"></div>
	<div class="col-md-4 schedule-box-live-container"></div>
	<div class="col-md-4 schedule-box-coming-up-container"></div>
</div>