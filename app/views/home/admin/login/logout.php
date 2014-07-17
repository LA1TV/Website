<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Logout</h3>
	</div>
	<div class="panel-body">
		<?php if ($loggedIn): ?>
		<p>You are currently logged in.</p>
		<?=FormHelpers::getFormSubmitButton(1, "Click Here To Logout", "", true);?>
		<?php else: ?>
		<p>You are logged out.</p>
		<a class="btn btn-info" href="<?=e(URL::to("/admin/login"));?>">Click Here To Login</a>
		<?php endif; ?>
		<?php if($showCosignLogoutButton): ?>
		<p>You are logged into cosign.</p>
		<?=FormHelpers::getFormSubmitButton(2, "Logout Of Cosign", "", true);?>
		<?php endif; ?>
	</div>
</div>