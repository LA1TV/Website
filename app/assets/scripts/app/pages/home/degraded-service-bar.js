define([
	"../../page-data",
	"./notification-bar",
	"./notification-priorities"
], function(PageData, NotificationBar, NotificationPriorities) {
	var degradedModeEnabled = PageData.get("degradedService");
	if (!degradedModeEnabled) {
		return;
	}
	var $outer = $("<div />").addClass("degraded-service-bar");
	var $container = $("<div />").addClass("container");
	var $glyph = $("<span />").addClass("glyphicon glyphicon-warning-sign icon");
	var $txt = $("<span />").text("We are currently running a degraded service.");
	$outer.append($container);
	$container.append($glyph);
	$container.append($txt);
	NotificationBar.createNotification($outer, NotificationPriorities.degradedService);
});