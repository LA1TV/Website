<?php if (!$episodeAccessibleToPublic): ?>
<div class="alert alert-info" role="alert"><strong>NOTE:</strong> This item is currently not accessible to the public.</div>
<?php endif; ?>
<div class="row">
	<div class="col-md-7">
		<h1 class="no-top-margin"><?=e($episodeTitle);?></h1>
		<?php if (!is_null($streamControlData)): ?>
		<div class="admin-panel panel-group custom-accordian" data-grouptogether="0" data-mediaitemid="<?=e($mediaItemId);?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Admin Stream Control</h4>
				</div>
				<div class="panel-collapse collapse">
					<div class="panel-body">
						<?php if ($streamControlData['showInaccessibleWarning']): ?>
						<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> The live stream is currently not accessible to the public no matter what the stream state is. This needs fixing in the control panel.</div>
						<?php endif; ?>
						<?php if ($streamControlData['showNoLiveStreamWarning']): ?>
						<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> There is currently no live stream attached to the live stream part of this media item.</div>
						<?php endif; ?>
						<?php if ($streamControlData['showLiveStreamNotAccessibleWarning']): ?>
						<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> There is a live stream attached to this media item, but it is not currently accessible. This needs fixing in the 'Live Streams' section of the control panel.</div>
						<?php endif; ?>
						<?php if ($streamControlData['showStreamReadyForLiveMsg']): ?>
						<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok"></span> This stream is all good to go live!</div>
						<?php endif; ?>
						<div class="my-row stream-state-row">
							<div>Stream state: <em>(Updates Instantly)</em></div>
							<div class="state-buttons" data-buttonsdata="<?=e(json_encode($streamControlData['streamStateButtonsData']));?>" data-chosenid="<?=e($streamControlData['streamStateChosenId']);?>"></div>
							
						</div>
						<div class="information-msg-section my-row clearfix">
							<div>Information message: (Shown When Not Live)</div>
							<textarea class="form-control" placeholder="Leave empty for no message."><?=e($streamControlData['streamInfoMsg']);?></textarea>
							<div class="buttons-row">
								<button type="button" class="revert-button btn btn-default btn-xs">Revert</button> <button type="button" class="update-button btn btn-primary btn-xs">Update Message</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="player-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-register-like-uri="<?=e($registerLikeUri);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>">
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
		<div class="comments" data-mediaitemid="<?=e($mediaItemId);?>">
			<div class="well well-sm">
				<table class="comments-table table table-bordered table-hover">
					<tbody>
						<tr>
							<td class="load-more-col" colspan="2">
								<button class="btn btn-info btn-sm btn-block" type="button">Load More</button>
							</td>
						</tr>
						<tr>
							<td class="profile-pic-col"><img class="img-responsive" src="https://graph.facebook.com/v2.1/4/picture?redirect=1&height=100&type=normal&width=100"></td>
							<td class="comment-box-col">
								<div class="comment-box">
									<div class="buttons-container">
										<div class="item">
											<button class="remove-btn btn btn-danger btn-xs" type="button">&times;</button>
										</div>
									</div>
									<div class="top-row"><span class="name">Tom Jenkinson</span> <span class="time">(10 minutes ago)</span></div>
									<div class="comment">The content of the comment.</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="profile-pic-col"><img class="img-responsive" src="https://graph.facebook.com/v2.1/779122855467151/picture?redirect=1&height=100&type=normal&width=100"></td>
							<td class="comment-box-col">
								<div class="comment-box">
									<div class="top-row"><span class="name">Tom Jenkinson</span> <span class="time">(10 minutes ago)</span></div>
									<div class="comment">The content of the comment.</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="new-comment-container clearfix">
					<input type="comment" class="form-control" placeholder="Enter comment...">
					<div class="buttons-row">
						<div class="item">
							<div class="checkbox">
								<label><input type="checkbox"> Post As Station</label>
							</div>
						</div>
						<div class="item">
							<button type="button" class="btn btn-primary btn-sm">Post</button>
						</div>
					</div>
				</div>
			</div>
		</div>
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

