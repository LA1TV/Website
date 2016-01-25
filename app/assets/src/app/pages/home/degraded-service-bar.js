var PageData = require("app/page-data");
var NotificationBar = require("./notification-bar");
var NotificationPriorities = require("./notification-priorities");
require("app/pages/home/degraded-service-bar.css");

var degradedModeEnabled = PageData.get("degradedService");
if (degradedModeEnabled) {
	var $outer = $("<div />").addClass("degraded-service-bar");
	var $container = $("<div />").addClass("container");
	var $glyph = $("<span />").addClass("glyphicon glyphicon-warning-sign icon");
	var $txt = $("<span />").text("We are currently running a degraded service.");
	$outer.append($container);
	$container.append($glyph);
	$container.append($txt);
	NotificationBar.createNotification($outer, NotificationPriorities.degradedService);
}