<div class="row">
	<div class="col-md-7">
		<h1 class="no-top-margin"><?=e($episodeTitle);?></h1>
		
		<div class="player-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>">
			<div class="embed-responsive embed-responsive-16by9">
				<div class="embed-responsive-item loading-container">
					<div class="msg">Player Loading</div>
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

