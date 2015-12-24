define([
	"jquery",
	"./page-data",
	"lib/socket.io"
], function($, PageData, io) {
	var url = PageData.get("notificationServiceUrl");
	var socket = null;
	if (url) {
		// enabled
		socket = io.connect(url);
	}

	return {
		on: function(eventName, handler) {
			if (socket) {
				socket.on(eventName, handler);
			}
		},
		off: function(eventName, handler) {
			if (socket) {
				socket.removeListener(eventName, handler);
			}
		}
	};
});