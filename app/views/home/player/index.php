<?php if (!$episodeAccessibleToPublic): ?>
<div class="alert alert-info" role="alert"><strong>NOTE:</strong> This item is currently not accessible to the public.</div>
<?php endif; ?>
<div class="row">
	<div class="col-md-7">
		<h1 class="no-top-margin"><?=e($episodeTitle);?></h1>
		<div class="admin-panel panel-group custom-accordian" data-grouptogether="0">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Admin Stream Control</h4>
				</div>
				<div class="panel-collapse collapse">
					<div class="panel-body">
						<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> The live stream is currently not accessible to the public. This needs fixing in the control panel.</div>
						<div class="my-row">
							<div>Stream state: <em>(Updates Instantly)</em></div>
							<div class="btn-group">
								<button type="button" class="btn btn-default">Not Live</button>
								<button type="button" class="btn btn-primary">Live</button>
								<button type="button" class="btn btn-default">Show Over</button>
							</div>
						</div>
						<div class="information-msg-section my-row clearfix">
							<div>Information message:</div>
							<textarea class="form-control"></textarea>
							<div class="buttons-row">
								<button type="button" class="btn btn-default btn-xs">Revert</button> <button type="button" class="btn btn-primary btn-xs">Update Message</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="player-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-register-like-uri="<?=e($registerLikeUri);?>">
			<div class="embed-responsive embed-responsive-16by9">
				<div class="embed-responsive-item loading-container">
					<div class="msg">Player Loading</div>
				</div>
			</div>
		</div>
<?php if (!is_null($episodeDescription)): ?>
		<div class="description-container"><?=e($episodeDescription);?></div>
<?php endif; ?>
		<h2>Comments</h2>
		<div class="comments">
			<div class="well well-sm">
				<div class="form-group">
					<input type="comment" class="form-control" placeholder="Enter comment...">
				</div>
			</div>
		</div>
		<p>Blah</p>
	</div>
	<div class="col-md-5">
		<div class="playlist">
			<table class="playlist-table table table-bordered table-striped table-hover">
				<thead>
					<tr class="button-row">
						<th class="clearfix" colspan="3">
							<div class="buttons">
								<div class="item">
									<button class="btn btn-default btn-xs" type="button">View All Series</button>
								</div>
								<div class="item">
									<?php if (!is_null($playlistPreviousItemUri)): ?>
									<a href="<?=e($playlistPreviousItemUri);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-backward"></span></a>
									<?php else: ?>
									<button disabled type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-backward"></span></button>
									<?php endif; ?>
								</div>
								<div class="item">
									<?php if (!is_null($playlistNextItemUri)): ?>
									<a href="<?=e($playlistNextItemUri);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-forward"></span></a>
									<?php else: ?>
									<button disabled type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-forward"></span></button>
									<?php endif; ?>
								</div>
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

