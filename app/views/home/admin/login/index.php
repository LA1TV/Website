<?php
	// TODO: REMOVE
	$cosignEnabled = true;
	$loggedIn = false;
	$accountDisabled = false;
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Login</h3>
	</div>
	<div class="panel-body">
		<?php if (!$loggedIn): ?>
		<?php if (Session::get("authRequestFromFilter", false)): ?>
		<div class="well well-sm">
			<span class="text-warning"><span class="glyphicon glyphicon-warning-sign"></span> You need to login in order to continue.</span>
		</div>
		<?php endif; ?>
		<h3>Login With Cosign</h3>
		<p>Login with your university account details.</p>
		<?php if ($cosignEnabled): ?>
		<a class="btn btn-primary" href="<?=e(Auth::getLoginUrl("admin/login"));?>">Login With Cosign</a>
		<?php else: ?>
		<button class="btn btn-default" type="button" disabled>Login With Cosign (Currently Unavailable)</button>
		<?php endif; ?>
		<h3>Login With Username and Password</h3>
		<p>Login with a username and password assigned to you for this site. <strong>This is not your university login.</strong></p>
		<?=FormHelpers::getFormTxtInput(1, "Username", "user", $form['user'], $formErrors);?>
		<?=FormHelpers::getFormPassInput(1, "Password", "pass", $form['pass'], $formErrors);?>
		<?=FormHelpers::getFormSubmitButton(1, "Login", "", true);?>
		<?php else: ?>
		<h3>Logged In!</h3>
		<?php if ($accountDisabled): ?>
		<p>You are logged in but your account is currently disabled. Please contact an admin.</p>
		<?php else: ?>
		<p>You are logged in!</p>
		<a href="<?=e(URL::to("/admin/dashboard"))?>" class="btn btn-primary">Click Here To Go To The Dashboard</a>
		<?php endif; ?>
		<?php endif; ?>
	</div>
</div>