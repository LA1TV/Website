self.addEventListener('message', function(event) {
	var command = event.data.command;
	var returnVal = null;
	if (command === "notificationsEnabled") {
		returnVal = notificationsEnabled();
	}
	event.ports[0].postMessage(returnVal);
});

// a notification MUST be triggered in this event handler
// because of the "userVisibleOnly" option (https://goo.gl/yqv4Q4)
// when push is subscribed in the ServiceWorker subscribeToPush()
// If notifications aren't triggered Chrome will display one of its own
self.addEventListener('push', function(event) {  
	// TODO

	console.log('Received a push message', event);
/*
	var title = 'Yay a message.';  
	var body = 'We have received a push message.';  
	var icon = '/images/icon-192x192.png';  
	var tag = 'simple-push-demo-notification-tag';

	event.waitUntil(  
		self.registration.showNotification(title, {  
			body: body,  
			icon: icon,  
			tag: tag  
		})  
	);
	*/ 
});

function notificationsEnabled() {
	return true;
}