<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Media</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Name", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormUploadInput(1, $coverImageUploadPointId, "Cover Image (Optional)", "cover-image-id", $form['cover-image-id'], $formErrors, AllowedFileTypesHelper::getImages(), $form['cover-image-file-name'], $form['cover-image-file-size'], !$editing);?>
		<?=FormHelpers::getFormUploadInput(1, $sideBannersImageUploadPointId, "Side Banners Image (Optional)", "side-banners-image-id", $form['side-banners-image-id'], $formErrors, AllowedFileTypesHelper::getImages(), $form['side-banners-image-file-name'], $form['side-banners-image-file-size'], !$editing);?>
		<div class="panel-group custom-accordian" data-grouptogether="0">
			<div class="panel panel-default vod-panel">
				<div class="panel-heading">
					<h4 class="panel-title">Video On Demand</h4>
				</div>
				<div class="panel-collapse collapse <?=$form['vod-added']==="1"?"in":""?>">
					<div class="panel-body">
						<div class="enabled-input hidden"><?=FormHelpers::getFormHiddenInput(1, "vod-added", $form['vod-added']);?></div>
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Video On Demand</button>
						</div>
						<div class="enabled-container">
							<button class="btn btn-default disable-button">Remove Video On Demand</button>
							<?=FormHelpers::getFormCheckInput(1, "Enabled", "vod-enabled", $form['vod-enabled']==="y", $formErrors);?>
							<?=FormHelpers::getFormTxtInput(1, "Name (Optional)", "vod-name", $form['vod-name'], $formErrors);?>
							<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "vod-description", $form['vod-description'], $formErrors);?>
							<?=FormHelpers::getFormUploadInput(1, $vodVideoUploadPointId, "Video", "vod-video-id", $form['vod-video-id'], $formErrors, AllowedFileTypesHelper::getVideos(), $form['vod-video-file-name'], $form['vod-video-file-size'], !$editing);?>
							<?=FormHelpers::getFormDateInput(1, "Time Recorded (Optional)", "vod-time-recorded", $form['vod-time-recorded'], $formErrors);?>
							<?=FormHelpers::getFormDateInput(1, "Scheduled Publish Time (Optional)", "vod-publish-time", $form['vod-publish-time'], $formErrors);?>
							<?=FormHelpers::getFormCheckInput(1, "Live Recording", "vod-live-recording", $form['vod-live-recording']==="y", $formErrors);?>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default live-stream-panel">
				<div class="panel-heading">
					<h4 class="panel-title">Live Stream</h4>
				</div>
				<div class="panel-collapse collapse <?=$form['stream-added']==="1"?"in":""?>">
					<div class="panel-body">
						<div class="enabled-input hidden"><?=FormHelpers::getFormHiddenInput(1, "stream-added", $form['stream-added']);?></div>
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Live Stream</button>
						</div>
						<div class="enabled-container">
							<button class="btn btn-default disable-button">Remove Live Stream</button>
							<?=FormHelpers::getFormCheckInput(1, "Enabled", "stream-enabled", $form['stream-enabled']==="y", $formErrors);?>
							<?=FormHelpers::getFormTxtInput(1, "Name (Optional)", "stream-name", $form['stream-name'], $formErrors);?>
							<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "stream-description", $form['stream-description'], $formErrors);?>
							<?=FormHelpers::getFormDateInput(1, "Scheduled Live Time (Optional)", "stream-live-time", $form['stream-live-time'], $formErrors);?>
							<?=FormHelpers::getFormSelectInput(1, "Stream", "stream-stream-id", $form['stream-stream-id'], $streamOptions, $formErrors);?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<button type="button" data-virtualform="1" data-virtualformsubmit="1" data-virtualformsubmitmethod="post" data-virtualformsubmitaction="" data-virtualformconfirm="" class="btn btn-primary"><?=$editing?"Update":"Create"?> Media</button>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>