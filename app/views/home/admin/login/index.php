<?php
	// TODO: REMOVE
	$form['user'] = "";
	$form['pass'] = "";
	$formErrors = null;
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Login</h3>
	</div>
	<div class="panel-body">
		<p>You need to login in order to continue.</p>
		<h3>Login With Cosign</h3>
		<p>Login with your university account details.</p>
		<a class="btn btn-primary" href="">Login With Cosign</a>
		
		<h3>Login With Username and Password</h3>
		<p>Login with a username and password assigned to you for this site. <strong>This is not your university login.</strong></p>
		<?=FormHelpers::getFormTxtInput(1, "Username", "user", $form['user'], $formErrors);?>
		<?=FormHelpers::getFormPassInput(1, "Password", "pass", $form['pass'], $formErrors);?>
		<?=FormHelpers::getFormSubmitButton(1, "Login", "", true);?>
	</div>
</div>