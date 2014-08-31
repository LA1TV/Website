<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Media</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Name", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormUploadInput(1, $coverImageUploadPointId, "Cover Image (Optional)", "cover-image-id", $form['cover-image-id'], $formErrors, $additionalForm['coverImageFile']['name'], $additionalForm['coverImageFile']['size'], !$additionalForm['coverImageFile']['inUse'], $additionalForm['coverImageFile']['processState'], $additionalForm['coverImageFile']['processPercentage'], $additionalForm['coverImageFile']['processMsg']);?>
		<?=FormHelpers::getFormUploadInput(1, $sideBannersImageUploadPointId, "Side Banners Image (Optional)", "side-banners-image-id", $form['side-banners-image-id'], $formErrors, $additionalForm['sideBannersImageFile']['name'], $additionalForm['sideBannersImageFile']['size'], !$additionalForm['sideBannersImageFile']['inUse'], $additionalForm['sideBannersImageFile']['processState'], $additionalForm['sideBannersImageFile']['processPercentage'], $additionalForm['sideBannersImageFile']['processMsg']);?>
		<?=FormHelpers::getFormDateInput(1, "Scheduled Publish/Live Time (Optional)", "publish-time", $form['publish-time'], $formErrors);?>
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
							<?=FormHelpers::getFormUploadInput(1, $vodCoverArtUploadPointId, "Cover Art (Optional)", "vod-cover-art-id", $form['vod-cover-art-id'], $formErrors, $additionalForm['vodCoverArtFile']['name'], $additionalForm['vodCoverArtFile']['size'], !$additionalForm['vodCoverArtFile']['inUse'], $additionalForm['vodCoverArtFile']['processState'], $additionalForm['vodCoverArtFile']['processPercentage'], $additionalForm['vodCoverArtFile']['processMsg']);?>
							<?=FormHelpers::getFormUploadInput(1, $vodVideoUploadPointId, "Video", "vod-video-id", $form['vod-video-id'], $formErrors, $additionalForm['vodVideoFile']['name'], $additionalForm['vodVideoFile']['size'], !$additionalForm['vodVideoFile']['inUse'], $additionalForm['vodVideoFile']['processState'], $additionalForm['vodVideoFile']['processPercentage'], $additionalForm['vodVideoFile']['processMsg']);?>
							<?=FormHelpers::getFormDateInput(1, "Time Recorded (Optional) (Must Be Empty When Recording Of Live Stream)", "vod-time-recorded", $form['vod-time-recorded'], $formErrors);?>
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
							<?=FormHelpers::getButtonGroupInput(1, "Current Status", "stream-state", $form['stream-state'], $formErrors, true, $additionalForm['streamStateButtonsData']);?>
							<?=FormHelpers::getFormCheckInput(1, "Being Recorded For VOD", "stream-being-recorded", $form['stream-being-recorded']==="y", $formErrors);?>
							<?=FormHelpers::getFormTxtInput(1, "Name (Optional)", "stream-name", $form['stream-name'], $formErrors);?>
							<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "stream-description", $form['stream-description'], $formErrors);?>
							<?=FormHelpers::getFormTxtAreaInput(1, "Information Message (Shown When Not Live) (Optional)", "stream-info-msg", $form['stream-info-msg'], $formErrors);?>
							<?=FormHelpers::getFormUploadInput(1, $streamCoverArtUploadPointId, "Cover Art (Optional)", "stream-cover-art-id", $form['stream-cover-art-id'], $formErrors, $additionalForm['streamCoverArtFile']['name'], $additionalForm['streamCoverArtFile']['size'], !$additionalForm['streamCoverArtFile']['inUse'], $additionalForm['streamCoverArtFile']['processState'], $additionalForm['streamCoverArtFile']['processPercentage'], $additionalForm['streamCoverArtFile']['processMsg']);?>
							<?=FormHelpers::getFormSelectInput(1, "Stream", "stream-stream-id", $form['stream-stream-id'], $streamOptions, $formErrors);?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?=FormHelpers::getFormGroupStart("related-items", $formErrors);
		?><label class="control-label">Related Media Items</label><div class="form-control form-related-items" data-initialdata="<?=e($additionalForm['relatedItemsInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "related-items", $additionalForm['relatedItemsInput']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "related-items"));?></div>
		
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." Media", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>