<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Live Stream</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Live Now", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Name", "name", $form['name'], $formErrors);?>
		<?=FormHelpers::getFormTxtAreaInput(1, "Description (Optional)", "description", $form['description'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Server Address (Can Include Port)", "server-address", $form['server-address'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Stream Name", "stream-name", $form['stream-name'], $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "DVR Enabled", "dvr-enabled", $form['dvr-enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormGroupStart("qualities", $formErrors);
		?><label class="control-label">Supported Qualities</label><div class="form-control form-qualities" data-initialdata="<?=e($additionalForm['qualitiesInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "qualities", $additionalForm['qualitiesInput']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "qualities"));?></div>
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