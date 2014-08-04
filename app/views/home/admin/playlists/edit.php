<?php
	// TODO: temporary
	$editing = false;
	$cancelUri = "";
	$formErrors = null;
	$form = array(
		"enabled"	=> "y",
		"name"		=> "",
		"description"	=> "",
		"cover-image-id"	=> "",
		"side-banners-image-id"	=> "",
		"cover-art-id"	=> "",
		"publish-time"	=> ""
	);
	
	$coverImageUploadPointId = 1;
	$sideBannersImageUploadPointId = 1;
	$coverArtUploadPointId = 1;
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Playlist</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Name (Optional)", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormUploadInput(1, $coverImageUploadPointId, "Cover Image (Optional)", "cover-image-id", $form['cover-image-id'], $formErrors, "", 0, 1, 0, null, null);?>
		<?=FormHelpers::getFormUploadInput(1, $sideBannersImageUploadPointId, "Side Banners Image (Optional)", "side-banners-image-id", $form['side-banners-image-id'], $formErrors, "", 0, 1, 0, null, null);?>
		<?=FormHelpers::getFormUploadInput(1, $coverArtUploadPointId, "Cover Art (Optional)", "cover-art-id", $form['cover-art-id'], $formErrors, "", 0, 1, 0, null, null);?>
		<?=FormHelpers::getFormDateInput(1, "Scheduled Publish Time (Optional)", "publish-time", $form['publish-time'], $formErrors);?>
		
		<?=FormHelpers::getFormGroupStart("playlist-content", $formErrors);
		?><label class="control-label">Playlist Content</label><div class="form-control form-playlist-content"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "playlist-content", ""));
		echo(FormHelpers::getErrMsgHTML($formErrors, "playlist-content"));?></div>
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