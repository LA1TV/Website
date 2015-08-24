<h1 class="no-top-margin"><?=e($title);?></h1>
<div class="row">
	<div class="col-md-12 player-container-col">
		<div class="player-container-component-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-watching-uri="<?=e($registerWatchingUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>">
			<div class="msg-container">
				<div class="embed-responsive embed-responsive-16by9">
					<div class="embed-responsive-item">
						<div class="msg msg-loading">Loading<br /><img src="<?=asset("assets/img/loading.gif");?>"></div>
					</div>
				</div>
			</div>
		</div>		
		<?php if (!is_null($descriptionEscaped)): ?>
		<div class="description-container">
			<?=$descriptionEscaped?>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-md-4 schedule-boxes hidden-col" data-schedule-uri="<?=e($scheduleUri);?>">
		<h2 class="title">What's On?</h2>
		<div class="schedule-box-live-container"></div>
		<div class="schedule-box-coming-up-container"></div>
		<div class="schedule-box-prev-live-container"></div>
	</div>
</div>