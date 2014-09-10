<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Edit Site User</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getReadOnlyTxtInput("Name", $additionalForm['name']);?>
		<?=FormHelpers::getReadOnlyTxtInput("First Name", $additionalForm['firstName']);?>
		<?=FormHelpers::getReadOnlyTxtInput("Last Name", $additionalForm['lastName']);?>
		<?=FormHelpers::getReadOnlyTxtInput("E-Mail", $additionalForm['email']);?>
		<?=FormHelpers::getFormCheckInput(1, "Banned", "banned", $form['banned'] === "y", $formErrors);?>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, "Update Site User", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>