<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Logout</h3>
	</div>
	<div class="panel-body">
		<?php if ($loggedIn): ?>
		<p>You are currently logged in.</p>
		<p><?=FormHelpers::getFormSubmitButton(1, "Click Here To Log Out", "", true);?></p>
		<?php else: ?>
		<p>You are logged out.</p>
		<p><a class="btn btn-info" href="<?=e(URL::to("/admin/login"));?>">Click Here To Login</a></p>
		<?php endif; ?>
		<?php if($showCosignLogoutButton): ?>
		<p>You are <?=!$loggedIn?" still":"also"?> logged into cosign.</p>
		<?php if ($loggedIn): ?>
		<p><em>Logging out of cosign will not log you out of the control panel.</em></p>
		<?php endif; ?>
		<p><?=FormHelpers::getFormSubmitButton(2, "Click Here To Log Out Of Cosign", "", true);?></p>
		<?php endif; ?>
	</div>
</div>