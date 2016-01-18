<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> API User</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Owner", "owner", $form['owner'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Key", "key", $form['key'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Information (Optional)", "information", $form['information'], $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "Can View VOD Uris", "can-view-vod-uris", $form['can-view-vod-uris'] === "y", $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "Can View Stream Uris", "can-view-stream-uris", $form['can-view-stream-uris'] === "y", $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "Can Use Webhooks", "can-use-webhooks", $form['can-use-webhooks'] === "y", $formErrors);?>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." API User", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>