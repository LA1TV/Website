var $ = require("jquery");
var PageData = require("./page-data");
var io = require("lib/socket.io");
var logger = require("app/logger");

var url = PageData.get("notificationServiceUrl");
var socket = null;
var connected = false;

if (url) {
	// enabled
	logger.debug("Connecting to notification service.");
	socket = io.connect(url);
	socket.on('connect', function() {
		logger.debug("Connected to notification service.");
		logger.debug("Sending authentication to notification service.");
		// authenticate with session id
		socket.emit('authentication', {
			sessionId: PageData.get("sessionId")
		});
		socket.once("authenticated", function() {
			connected = true;
			logger.debug("Authenticated with notification service.");
		});
	});
	socket.on('connect_error', function() {
		logger.warn("Notification service connection attempt failed.");
	});
	socket.on('disconnect', function() {
		connected = false;
		logger.info("Notification service connection lost.");
	});
}

module.exports = {
	isConnected: function() {
		return connected;
	},
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