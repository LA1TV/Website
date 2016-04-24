<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Live Stream</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Live Now", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "Show As Livestream On Site", "shownAsLivestream", $form['shownAsLivestream'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Name", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormUploadInput(1, $coverArtUploadPointId, "Cover Art (Optional) (Should Be 16:9)", "cover-art-id", $form['cover-art-id'], $formErrors, $additionalForm['coverArtFile']['name'], $additionalForm['coverArtFile']['size'], $additionalForm['coverArtFile']['processState'], $additionalForm['coverArtFile']['processPercentage'], $additionalForm['coverArtFile']['processMsg']);?>
		<?=FormHelpers::getAjaxSelectInput(1, "Inherited Live Media Item (Optional)", "inherited-live-media-item-id", $form['inherited-live-media-item-id'], $formErrors, $mediaItemsAjaxSelectDataUri, $additionalForm['inheritedLiveMediaItemText'], "form-inherited-live-media-item");?>
		<?=FormHelpers::getFormGroupStart("urls", $formErrors);
		?><label class="control-label">Urls and Qualities</label><div class="form-control form-urls" data-initialdata="<?=e($additionalForm['urlsInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "urls", $additionalForm['urlsInput']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "urls"));?></div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">	
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." Live Stream", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>