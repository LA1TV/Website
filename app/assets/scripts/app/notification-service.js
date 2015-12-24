define([
	"jquery",
	"./page-data",
	"lib/socket.io"
], function($, PageData, io) {
	var url = PageData.get("notificationServiceUrl");
	if (!url) {
		// disabled
		return;
	}
	var socket = io.connect(url);
	return {
		on: function(eventName, handler) {
			socket.on(eventName, handler);
		},
		off: function(eventName, handler) {
			socket.removeListener(eventName, handler);
		}
	};
});