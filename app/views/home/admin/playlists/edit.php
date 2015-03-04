<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Playlist</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>	
		<?=FormHelpers::getAjaxSelectInput(1, "Show (Optional)", "show-id", $form['show-id'], $formErrors, $seriesAjaxSelectDataUri, $additionalForm['showItemText'], "form-show");?>
		<div class="series-no-container">
			<?=FormHelpers::getFormTxtInput(1, "Series Number", "series-no", $form['series-no'], $formErrors, "number");?>
		</div>
		<?=FormHelpers::getFormTxtInput(1, "Name (Optional If Part Of Series)", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Custom Uri (Optional)", "custom-uri", $form['custom-uri'], $formErrors);?>
		<?=FormHelpers::getFormUploadInput(1, $coverImageUploadPointId, "Cover Image (Optional) (Should Be 940x150)", "cover-image-id", $form['cover-image-id'], $formErrors, $additionalForm['coverImageFile']['name'], $additionalForm['coverImageFile']['size'], !$additionalForm['coverImageFile']['inUse'], $additionalForm['coverImageFile']['processState'], $additionalForm['coverImageFile']['processPercentage'], $additionalForm['coverImageFile']['processMsg']);?>
		<?=FormHelpers::getFormUploadInput(1, $sideBannersImageUploadPointId, "Side Banners Image (Optional) (Should Be 315x851)", "side-banners-image-id", $form['side-banners-image-id'], $formErrors, $additionalForm['sideBannersImageFile']['name'], $additionalForm['sideBannersImageFile']['size'], !$additionalForm['sideBannersImageFile']['inUse'], $additionalForm['sideBannersImageFile']['processState'], $additionalForm['sideBannersImageFile']['processPercentage'], $additionalForm['sideBannersImageFile']['processMsg']);?>
		<?=FormHelpers::getFormUploadInput(1, $coverArtUploadPointId, "Cover Art (Optional) (Should Be 16:9)", "cover-art-id", $form['cover-art-id'], $formErrors, $additionalForm['coverArtFile']['name'], $additionalForm['coverArtFile']['size'], !$additionalForm['coverArtFile']['inUse'], $additionalForm['coverArtFile']['processState'], $additionalForm['coverArtFile']['processPercentage'], $additionalForm['coverArtFile']['processMsg']);?>
		<?=FormHelpers::getFormDateInput(1, "Scheduled Publish Time (Optional)", "publish-time", $form['publish-time'], $formErrors);?>
		
		<?=FormHelpers::getFormGroupStart("playlist-content", $formErrors);
		?><label class="control-label">Playlist Content</label><div class="form-control form-playlist-content" data-initialdata="<?=e($additionalForm['playlistContentInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "playlist-content", $additionalForm['playlistContentInput']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "playlist-content"));?></div>
	
		<?=FormHelpers::getFormGroupStart("related-items", $formErrors);
		?><label class="control-label">Related Media Items</label><div class="form-control form-related-items" data-initialdata="<?=e($additionalForm['relatedItemsInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "related-items", $additionalForm['relatedItemsInput']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "related-items"));?></div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." Playlist", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>