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

<div class="row">
	<div class="col-md-4">
		<div class="schedule-box previously-live">
			<div class="embed-responsive embed-responsive-16by9 window" data-jslink="http://google.com">
				<div class="art-container">
					<img src="https://www.la1tv.co.uk/file/25125">
				</div>
				<div class="overlay overlay-top">
					<h2 class="box-name">Previously</h2>
					<h4 class="box-time">12:20</h4>
				</div>
				<div class="overlay overlay-bottom">
					<h3 class="box-show-name">UniBrass 2016</h3>
					<div class="box-episode-name">Episode 5 (Freshers Week Special)</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="schedule-box live-now">
			<div class="embed-responsive embed-responsive-16by9 window" data-jslink="http://google.com">
				<div class="art-container">
					<img src="https://www.la1tv.co.uk/file/25125">
				</div>
				<div class="overlay overlay-top">
					<h2 class="box-name">Live Now</h2>
					<h4 class="box-time">12:20</h4>
				</div>
				<div class="overlay overlay-bottom">
					<h3 class="box-show-name">UniBrass 2016</h3>
					<div class="box-episode-name">Episode 5 (Freshers Week Special)</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="schedule-box coming-up">
			<div class="embed-responsive embed-responsive-16by9 window" data-jslink="http://google.com">
				<div class="art-container">
					<img src="https://www.la1tv.co.uk/file/25125">
				</div>
				<div class="overlay overlay-top">
					<h2 class="box-name">Up Next</h2>
					<h4 class="box-time">12:20</h4>
				</div>
				<div class="overlay overlay-bottom">
					<h3 class="box-show-name">UniBrass 2016</h3>
					<div class="box-episode-name">Episode 5 (Freshers Week Special)</div>
				</div>
			</div>
		</div>
	</div>
</div>