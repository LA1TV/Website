<?php if (!$episodeAccessibleToPublic): ?>
<div class="alert alert-info" role="alert"><strong>NOTE:</strong> This item is currently not accessible to the public.</div>
<?php endif; ?>
<?php if (!is_null($coverImageUri)): ?>
	<div class="cover-image-container">
		<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
	</div>
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
		<div class="player-container-component-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-view-count-uri="<?=e($registerViewCountUri);?>" data-register-like-uri="<?=e($registerLikeUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>">
			<div class="msg-container">
				<div class="embed-responsive embed-responsive-16by9">
					<div class="embed-responsive-item">
						<div class="msg msg-loading">Player Loading</div>
					</div>
				</div>
			</div>
		</div>
<?php if (!is_null($episodeDescription)): ?>
		<div class="description-container"><?=e($episodeDescription);?></div>
<?php endif; ?>
		<h2>Comments</h2>
		<div class="comments-container" data-media-item-id="<?=e($mediaItemId);?>" data-get-uri="<?=e($getCommentsUri);?>" data-post-uri="<?=e($postCommentUri);?>" data-delete-uri="<?=e($deleteCommentUri);?>" data-can-comment-as-facebook-user="<?=$canCommentAsFacebookUser?"1":"0"?>" data-can-comment-as-station="<?=$canCommentAsStation?"1":"0"?>"></div>
	</div>
	<div class="col-md-5">
		<div class="playlist">
			<div class="button-row clearfix">
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
			</div>
			<div class="playlist-table-container">
				<table class="playlist-table table table-bordered table-striped table-hover">
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
		<?php if (count($relatedItemsTableData) > 0): ?>
		<div class="playlist">
			<div class="button-row clearfix">
				<h2 class="playlist-title">Related Items</h2>
			</div>
			<div class="playlist-table-container">
				<table class="playlist-table table table-bordered table-striped table-hover">
					<tbody>
						<?php foreach($relatedItemsTableData as $row):?>
						<tr>
							<td class="col-episode-no"><?=e($row['episodeNo'])?>.</td>
							<td class="col-thumbnail"><a href="<?=e($row['uri']);?>"><img class="img-responsive" src="<?=e($row['thumbnailUri']);?>"/></a></td>
							<td class="col-title clearfix">
								<div class="subtitle"><span class="label label-info"><?=e($row['playlistName']);?></div></div>
								<div class="title"><?=e($row['title']);?></div>
							</td>
						</tr>
						<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>

