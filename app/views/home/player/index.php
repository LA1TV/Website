<div class="row">
	<div class="col-md-7">
		<h1 class="no-top-margin"><?=e($episodeTitle);?></h1>
		
		<div class="player-container" data-info-uri="<?=e($playerInfoUri);?>"></div>
		
		<?php /*
		<div class="player-container embed-responsive embed-responsive-16by9">
			<div class="player embed-responsive-item" data-coveruri="<?=e($episodeCoverArtUri);?>" data-autoplay="">
				<video class="video-js vjs-default-skin">
					<source src="<?=e($episodeUri);?>" type='video/mp4'/>
					<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				</video>
			</div>
		*/ ?>	
			
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
		</div>
		*/ ?>
		
		<div class="bottom-info-container clearfix">
			<div class="view-count">127 views</div>
			<div class="buttons">
				<div class="button-container">
					<button class="btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-thumbs-up"></span> Like!</button>
				</div>
			</div>
		</div>
		<div class="description-container"><?=e($episodeDescription);?></div>
		<h2>Comments</h2>
		<p>Blah</p>
	</div>
	<div class="col-md-5">
		<div class="playlist">
			<table class="playlist-table table table-bordered table-striped table-hover">
				<thead>
					<tr class="button-row">
						<th class="clearfix" colspan="3">
							<div class="buttons">
								<button class="btn btn-default btn-xs" type="button">View All Series</button> <button class="btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-fast-backward"></span></button> <button class="btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-fast-forward"></span></button>
							</div>
							<h2 class="playlist-title"><?=e($playlistTitle);?></h2>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($playlistTableData as $row):?>
					<tr class="<?=$row['active'] ? "chosen" : ""?>">
						<td class="col-episode-no"><?=e($row['episodeNo'])?>.</td>
						<td class="col-thumbnail"><a href="<?=e($row['uri']);?>"><img class="img-responsive" src="<?=e($row['thumbnailUri']);?>"/></a></td>
						<td class="col-title"><?=e($row['title']);?></td>
					</tr>
					<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

