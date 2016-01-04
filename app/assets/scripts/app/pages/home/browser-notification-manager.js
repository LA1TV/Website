define([
	"jquery",
	"./notification-bar",
	"./notification-priorities",
	"../../notification-service",
], function($, NotificationBar, NotificationPriorities, NotificationService) {
	var N = Notification;
	if (!N || !N.requestPermission) {
		// html5 browser notifications not supported
		return;
	}
	$permissionRequestBar = null;

	var requestBarHandle = null;
	if (needPermission() && canRequestPermission()) {
		requestBarHandle = NotificationBar.createNotification(getRequestBarEl(), NotificationPriorities.notificationsPermissionRequest);
	}

	function onPermissionGranted() {
		requestBarHandle.remove();
	}

	function onPermissionDenied() {
		requestBarHandle.remove();
	}

	function needPermission() {
		return N && N.permission && N.permission !== 'granted';
	}

	// permission request dialog will only be shown if the user hasn't explicitly denied permission
	// ie "default" state
	function canRequestPermission() {
		return N && N.permission && N.permission === 'default';
	}

	function requestPermission(grantedCallback, deniedCallback) {
		N.requestPermission(function(perm) {
			if (perm === "granted") {
				grantedCallback && grantedCallback();
			}
			else if (perm === "denied") {
				deniedCallback && deniedCallback();
			}
        });
	}

	function getRequestBarEl() {
		if ($permissionRequestBar) {
			return $permissionRequestBar;
		}
		$permissionRequestBar = $("<div />").addClass("browser-notification-permission-request-bar");
		var $container = $("<div />").addClass("container");
		var $line1 = $("<div />").text("We would like to be able to send you notifications.");
		var $line2 = $("<div />").addClass("info-txt").text("Click here so we can notify you when a stream goes live.").addClass("hidden");
		$container.append($line1);
		$container.append($line2);
		$permissionRequestBar.append($container);

		$permissionRequestBar.hover(function() {
			$line2.removeClass("hidden");
			requestBarHandle.onHeightChanged();
		}, function() {
			$line2.addClass("hidden");
			requestBarHandle.onHeightChanged();
		});

		$permissionRequestBar.click(function() {
			requestPermission(onPermissionGranted, onPermissionDenied);
		});
		return $permissionRequestBar;
	}
});