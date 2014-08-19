<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> CMS User</h3>
	</div>
	<div class="panel-body">
		<?=FormHelpers::getFormCheckInput(1, "Enabled", "enabled", $form['enabled'] === "y", $formErrors);?>
		<?=FormHelpers::getFormCheckInput(1, "Admin", "admin", $form['admin'] === "y", $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Cosign Username (Optional)", "cosign-user", $form['cosign-user'], $formErrors);?>
		<?=FormHelpers::getFormTxtInput(1, "Username (Optional)", "user", $form['user'], $formErrors);?>
		<?=FormHelpers::getFormGroupStart("password", $formErrors);
		?><label class="control-label">Password (Required With Username)</label><div class="form-control form-password" data-toggleenabled="<?=$additionalForm['passwordToggleEnabled']?"1":"0"?>" data-initialdata="<?=e($additionalForm['passwordInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "password", $form['password']));
		echo(FormHelpers::getFormHiddenInput(1, "password-changed", $additionalForm['passwordChanged']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "password"));?></div>
		
		<?=FormHelpers::getFormGroupStart("groups", $formErrors);
		?><label class="control-label">Groups</label><div class="form-control form-groups" data-initialdata="<?=e($additionalForm['groupsInitialData'])?>"></div><?php
		echo(FormHelpers::getFormHiddenInput(1, "groups", $form['groups']));
		echo(FormHelpers::getErrMsgHTML($formErrors, "groups"));?></div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." CMS User", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>