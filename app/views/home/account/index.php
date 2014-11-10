<h1>Account Settings</h1>
<?php if ($loggedIn): ?>
<h2>Email Notifications</h2>
<p>Enabling this allows us to send you emails to the address you registered with facebook. For example whenever new content becomes available or a show goes live.</p>
<div class="email-notifications-button-group-container" data-buttonsdata="<?=e(json_encode($emailNotificationsButtonsData));?>" data-chosenid="<?=e($emailNotificationsButtonsInitialId);?>"></div>
<?php if (!$haveEmailPermission): ?>
<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> We don't have permission from facebook to access your email address yet and this is needed in order to send you emails. <a href="<?=e(Url::to("/facebook/permissions"));?>">Click here to provide this</a>.</div>
<?php endif; ?>
<h2>Logout</h2>
<a class="btn btn-default" href="<?=e($logoutUri);?>" data-confirm="Are you sure you want to log out of the website?">Click Here To Logout</a>
<?php else: ?>
<p>This page is only accessible when you are logged in.</p>
<?php endif; ?>