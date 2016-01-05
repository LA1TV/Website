define([
	"jquery",
	"./notification-bar",
	"./notification-priorities",
	"../../notification-service",
	"../../page-data",
], function($, NotificationBar, NotificationPriorities, NotificationService, PageData) {
	var N = ("Notification" in window) ? window.Notification : null;
	if (!N || !N.requestPermission) {
		// html5 browser notifications not supported
		return;
	}

	if (needPermission() && !canRequestPermission()) {
		// user denied notifications
		return;
	}

	var $permissionRequestBar = null;
	var lastNotificationId = 0;
	var iconUrl = PageData.get("assetsBaseUrl")+"assets/img/notification-icon.png";
	var soundUrl = PageData.get("assetsBaseUrl")+"assets/audio/notification.mp3";
	var $soundFX = null;

	var requestBarHandle = null;
	if (needPermission() && canRequestPermission()) {
		requestBarHandle = NotificationBar.createNotification(getRequestBarEl(), NotificationPriorities.notificationsPermissionRequest);
	}
	else {
		listenToEvents();
	}

	function onPermissionGranted() {
		requestBarHandle.remove();
		setTimeout(function() {
			createNotification("Notifications Enabled", "Thanks for letting us send you notifications.");
		}, 1000);
		listenToEvents();
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

	function listenToEvents() {
		NotificationService.on("mediaItem.live", function(data) {
			createNotification("We are live!", 'We are live now with "'+data.name+'".', data.url);
		});
		NotificationService.on("mediaItem.vodAvailable", function(data) {
			createNotification("New content available!", '"'+data.name+'" is now available to watch on demand.', data.url);
		});
	}

	function createNotification(title, message, link) {
		var n = new N(title, {
			lang: "EN",
			body: message,
			tag: ""+(++lastNotificationId),
			icon: iconUrl
		});

		var timerId = null;

		if (link) {
			n.addEventListener("click", function() {
				window.location.href = link;
				n.close();
			});
		}

		n.addEventListener("show", function() {
			if (!$soundFX) {
				$soundFX = $("<audio />").attr("volume", 1).attr("src", soundUrl).attr("preload", "auto").prop("autoplay", true).hide();
				$("body").append($soundFX);
			}
			else {
				$soundFX[0].currentTime = 0;
				$soundFX[0].play();
			}
			timerId = setTimeout(function() {
				timerId = null;
				n.close();
			}, 8000);
		});

		n.addEventListener("close", function() {
			if (timerId !== null) {
				clearTimeout(timerId);
			}
		});
	}

	function getRequestBarEl() {
		if ($permissionRequestBar) {
			return $permissionRequestBar;
		}
		$permissionRequestBar = $("<div />").addClass("browser-notification-permission-request-bar");
		var $container = $("<div />").addClass("container");
		var $glyph = $("<span />").addClass("glyphicon glyphicon-bullhorn icon");
		var $line1 = $("<span />").text("Click to enable browser notifications.");
		$container.append($glyph);
		$container.append($line1);
		$permissionRequestBar.append($container);

		$permissionRequestBar.click(function() {
			requestPermission(onPermissionGranted, onPermissionDenied);
		});
		return $permissionRequestBar;
	}
});