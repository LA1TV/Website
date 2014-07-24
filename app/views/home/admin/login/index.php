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
		<?php if (!$cosignEnabled || !$loggedIntoCosignAsUnknownUser): ?>
		<p>Login with your university account details.</p>
		<?php endif; ?>
		<?php if ($cosignEnabled): ?>
		<?php if ($loggedIntoCosignAsUnknownUser): ?>
		<p>You are currently logged into cosign as "<?=e(Auth::getCosignUser())?>" but this user does not currently have access to this control panel.</p>
		<p><?=FormHelpers::getFormSubmitButton(3, "Logout Of Cosign", "", false);?></p>
		<?php else: ?>
		<p><?=FormHelpers::getFormSubmitButton(2, "Login With Cosign", "", true);?></p>
		<?php endif; ?>
		<?php else: ?>
		<p><button class="btn btn-default" type="button" disabled>Login With Cosign (Currently Unavailable)</button></p>
		<?php endif; ?>
		<h3>Login With Username and Password</h3>
		<p>Login with a username and password assigned to you for this control panel. <strong>This is not your university login.</strong></p>
		<?=FormHelpers::getFormTxtInput(1, "Username", "user", $form['user'], $formErrors);?>
		<?=FormHelpers::getFormPassInput(1, "Password", "pass", $form['pass'], $formErrors);?>
		<p><?=FormHelpers::getFormSubmitButton(1, "Login", "", true);?></p>
		<?php else: ?>
		<?php if ($accountDisabled): ?>
		<p>You are logged in but your account is currently disabled. Please contact an admin.</p>
		<?php else: ?>
		<p>You are logged in!</p>
		<p><a href="<?=e(URL::to("/admin/dashboard"))?>" class="btn btn-primary">Click Here To Go To The Dashboard</a></p>
		<?php endif; ?>
		<?php endif; ?>
	</div>
</div>