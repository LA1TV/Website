<h1><?=e($title);?></h1>
<div class="row">
	<div class="col-md-7">
		<div class="player-container embed-responsive embed-responsive-16by9">
			<div class="player embed-responsive-item" data-coveruri="<?=asset("assets/tmp/cover.png");?>" data-autoplay="">
				<video class="video-js vjs-default-skin ">
					<source src="http://video-js.zencoder.com/oceans-clip.mp4" type='video/mp4' />
					<source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />
					<source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />
					<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				</video>
			</div>
			
			
			<?php /*
			<div class="ad embed-responsive-item">
				<div class="bg">
					<!--<img class="img-responsive" src="<?=asset("assets/img/default-cover.png");?>"/>-->
					<img class="img-responsive" src="<?=asset("assets/tmp/cover.png");?>"/>
				</div>
				<div class="overlay">
					<div class="live-at-header fit-text txt-shadow" data-compressor="1.5">Live In</div>
					<!--<div class="live-at-header fit-text txt-shadow" data-compressor="2.8">Available To Watch On Demand Shortly</div>-->
					<div class="live-time fit-text txt-shadow" data-compressor="2.1">10 min 15 sec</div>
					<!--<div class="custom-msg fit-text txt-shadow" data-compressor="2.8">The world is ending.</div>-->
				</div>
			</div>
			*/ ?>
		</div>
	</div>
	<div class="col-md-5">
		[Playlist Here]
	</div>
</div>
