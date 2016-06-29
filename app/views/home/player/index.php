<?php if (!is_null($coverImageUri)): ?>
	<div class="cover-image-container">
		<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
	</div>
<?php endif; ?>
<div class="row">
	<div class="col-md-7">
		<h1 class="no-top-margin"><?=e($episodeTitle);?></h1>
		<?php if (!is_null($streamControlData) || !is_null($vodControlData)): ?>
		<div class="admin-panel panel-group custom-accordian" data-grouptogether="1" data-mediaitemid="<?=e($mediaItemId);?>">
			<?php if (!is_null($vodControlData)): ?>
			<div class="panel panel-default vod-control">
				<div class="panel-heading">
					<h4 class="panel-title">Admin: Video On Demand</h4>
				</div>
				<div class="panel-collapse collapse">
					<div class="panel-body">
						<div class="my-row vod-upload-row">
							<div>Video:</div>
							<?=FormHelpers::getFileUploadRawElement("vod-upload-component", $vodControlData['uploadPointId'], $vodControlData['info']['name'], $vodControlData['info']['size'], $vodControlData['fileId'], $vodControlData['info']['processState'], $vodControlData['info']['processPercentage'], $vodControlData['info']['processMsg']);?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!is_null($streamControlData)): ?>
			<div class="panel panel-default stream-control" data-beingrecordedforvod="<?=$beingRecordedForVod?"1":"0"?>">
				<div class="panel-heading">
					<h4 class="panel-title">Admin: Live Stream</h4>
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
						<?php if ($streamControlData['showExternalStreamLocationMsg']): ?>
						<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> This stream is hosted at an external location. Users will be shown a button to take them to this location instead of the player.</div>
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
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<style>
			.suggestions-overlay {
				height: 348px;
				margin-bottom: 100px;

				position: relative;
				text-align: center;
				background-color: #000000;
				color: #ffffff;
				overflow: hidden;
			}

			.suggestions-overlay .restart-container {
				position: absolute;
				display: inline-block;
				padding: 5px 10px;
				background-color: rgba(0, 0, 0, 0.5);
				bottom: 5px;
				left: 5px;
				cursor: pointer;
				box-shadow: 0px 0px 8px 1px rgba(0, 0, 0, 0.4);
			}

			.suggestions-overlay .restart-container:hover {
				background-color: rgba(0, 0, 0, 0.8);
			}

			.suggestions-overlay .background {
				position: absolute;
				top: -10px;
				bottom: -10px;
				left: -10px;
				right: -10px;
				background-image: url("https://www.la1tv.co.uk/file/91745");
				background-position: center;
				background-size: cover;
				filter: blur(5px);
				-webkit-filter: blur(5px); /* TODO remove and fix above */
				opacity: 0.7;
			}

			.suggestions-overlay .recommended-title {
				position: absolute;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				opacity: 0.7;
			}

			.suggestions-overlay .recommended-title .title {
				text-align: left;
				font-weight: bold;
				font-style: italic;
				font-size: 22px;
				text-shadow: 0px 0px 7px rgba(0, 0, 0, 0.77);
				margin: 10px 10px 0px 10px;
			}

			.suggestions-overlay .background.darken {
				opacity: 0.5;
			}

			.suggestions-overlay .top-layer {
				position: absolute;
				width: 75%;
				max-width: 410px;
				top: 50%;
				left: 50%;
				padding: 0 50px;
				transform: translateX(-50%) translateY(-50%) scale(1);
			}

			.suggestions-overlay .arrow-container {
				position: absolute;
				top: 0;
				bottom: 0;
				width: 50px;
				cursor: pointer;
			}

			.suggestions-overlay .left-arrow-container {
				left: 0;
			}

			.suggestions-overlay .right-arrow-container {
				right: 0;
			}

			.suggestions-overlay .top-layer .arrow {
				position: absolute;
				width: 100%;
				top: 50%;
				text-align: center;
				vertical-align: middle;
				font-size: 40px;
				font-weight: bold;
				opacity: 0.6;
				transform: translateY(-50%);
				text-shadow: 0px 0px 7px rgba(0, 0, 0, 0.77);
			}

			.suggestions-overlay .top-layer .left-arrow-container .left-arrow {
				left: 0;
				transition: opacity 0.08s ease-in-out, left 0.08s ease-in-out;
			}

			.suggestions-overlay .top-layer .left-arrow-container:hover .left-arrow {
				left: -10px;
				opacity: 1;
			}

			.suggestions-overlay .top-layer .right-arrow-container .right-arrow {
				right: 0;
				transition: opacity 0.08s ease-in-out, right 0.08s ease-in-out;
			}

			.suggestions-overlay .top-layer .right-arrow-container:hover .right-arrow {
				right: -10px;
				opacity: 1;
			}

			.suggestions-overlay .top-layer .suggestion {
				transform: scale(1);
				background-color: rgba(0, 0, 0, 0.8);
				cursor: pointer;
				transition: transform 0.08s ease-in-out;
				box-shadow: 0px 0px 14px 2px rgba(0, 0, 0, 0.65);
			}

			.suggestions-overlay .top-layer .suggestion:hover {
				transform: scale(1.15);
			}

			.suggestions-overlay .top-layer .suggestion .art {
				
			}

			.suggestions-overlay .top-layer .suggestion .title {
				position: absolute;
				left: 0;
				right: 0;
				bottom: 0;
				padding: 5px 5px;
				background-color: rgba(0, 0, 0, 0.5);
				overflow: auto;
				max-height: 100%;
				font-size: 12px;
			}

			@media (min-width: 450px) {
				.suggestions-overlay .recommended-title .title{
					font-size: 30px;
				}

				.suggestions-overlay .top-layer .suggestion .title {
					font-size: 20px;
				}
			}


		</style>

		<div class="suggestions-overlay">
			<div class="background"></div>
			<div class="recommended-title">
				<h3 class="title">Recommended For You</h3>
			</div>
			<div class="top-layer">
				<div class="arrow-container left-arrow-container">
					<div class="arrow left-arrow">
						&#x3C;
					</div>
				</div>
				<div class="arrow-container right-arrow-container">
					<div class="arrow right-arrow">&#x3E;</div>
				</div>
				<div class="suggestion">
					<div class="art">
						<img class="img-responsive" src="https://www.la1tv.co.uk/file/91745">
					</div>
					<div class="title">
						19) Grizedale
					</div>
				</div>
			</div>
			<div class="restart-container">
				<span class="glyphicon glyphicon-repeat"></span> Watch Again
			</div>
		</div>
		<div class="player-container-component-container" data-info-uri="<?=e($playerInfoUri);?>" data-register-watching-uri="<?=e($registerWatchingUri);?>" data-register-like-uri="<?=e($registerLikeUri);?>" data-login-required-msg="<?=e($loginRequiredMsg);?>" data-enable-admin-override="<?=$adminOverrideEnabled?"1":"0"?>" data-auto-play-vod="<?=$autoPlay?"1":"0"?>" data-vod-play-start-time="<?=$vodPlayStartTime?>">
			<div class="msg-container">
				<div class="embed-responsive embed-responsive-16by9">
					<div class="embed-responsive-item">
						<div class="msg msg-loading">Loading<br /><img src="<?=asset("assets/img/loading.gif");?>"></div>
					</div>
				</div>
			</div>
		</div>
<?php if (!is_null($episodeDescriptionEscaped)): ?>
		<div class="description-container"><?=$episodeDescriptionEscaped;?></div>
<?php endif; ?>
<?php if (count($vodChapters) > 0): ?>
	<h2>Chapter Selection</h2>
	<p>Click on a chapter to jump to a specific point in the video.</p>
	<table class="chapter-selection-table table table-bordered table-hover table-striped">
		<tbody>	
	<?php foreach($vodChapters as $a): ?>
			<tr data-time="<?=e($a['time']);?>">
				<td><?=e($a['num']);?>)</td>
				<td><?=e($a['title']);?> <span class="time-str">(<?=e($a['timeStr']);?>)</span></td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
<?php if ($commentsEnabled): ?>
		<h2>Comments</h2>
		<div class="comments-container" data-media-item-id="<?=e($mediaItemId);?>" data-get-uri="<?=e($getCommentsUri);?>" data-post-uri="<?=e($postCommentUri);?>" data-delete-uri="<?=e($deleteCommentUri);?>" data-can-comment-as-facebook-user="<?=$canCommentAsFacebookUser?"1":"0"?>" data-can-comment-as-station="<?=$canCommentAsStation?"1":"0"?>">
			<div class="well well-sm">
				<em>Loading...</em>
			</div>
		</div>
<?php endif; ?>
	</div>
	<div class="col-md-5">
	<?php if (!is_null($seriesAd)): ?>
		<div class="go-to-series-btn-container">
			<a class="btn btn-info btn-block" href="<?=e($seriesAd['uri']);?>">Go To "<?=e($seriesAd['name']);?>"</a>
		</div>
	<?php endif; ?>
		<div class="playlist" data-info-uri="<?=e($playlistInfoUri);?>" data-auto-continue-mode="<?=e($autoContinueMode);?>">
			<?=$playlistTableFragment?>
		</div>
		<?php if (!is_null($relatedItemsTableFragment)): ?>
		<div class="related-items">
			<?=$relatedItemsTableFragment?>
		</div>
		<?php endif; ?>
	</div>
</div>