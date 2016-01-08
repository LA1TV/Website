self.addEventListener('message', function(event) {
	var data = event.data;
	var command = data.command;
	var returnVal = null;
	
	// currently does nothing

	event.ports[0].postMessage(returnVal);
});

// a notification MUST be triggered in this event handler
// because of the "userVisibleOnly" option (https://goo.gl/yqv4Q4)
// when push is subscribed in the ServiceWorker subscribeToPush()
// If notifications aren't triggered Chrome will display one of its own
self.addEventListener('push', function(event) {
	event.waitUntil(makeRequest("/ajax/notifications").then(function(notificationsData) {
		if (notificationsData.length === 0) {
			return showErrorNotification();
		}

		// show all pending notifications
		return Promise.all(notificationsData.map(function(notificationData) {
			return self.registration.showNotification(notificationData.title, {  
				body: notificationData.body,  
				icon: notificationData.iconUrl,
				data: {
					url: notificationData.url
				}
			});
		}));

	}).catch(function(e) {
		return showErrorNotification();
	}));
});

self.addEventListener('notificationclick', function(event) {
	var data = event.notification.data;
	var url = "url" in data ? data.url : null;
	if (!url) {
		return;
	}

	event.notification.close();
	// This looks to see if the current is already open and  
  	// focuses if it is  
	event.waitUntil(
	clients.matchAll({  
		type: "window"  
	}).then(function(clientList) {
		for (var i=0; i<clientList.length; i++) {
			var client = clientList[i];
			if (client.url === url && 'focus' in client) {
				// focus the client that is already at the url
				return client.focus();
			}
		}
		if (clients.openWindow) {
			return clients.openWindow(url);  
		}
	}));
});

function makeRequest(url) {
	return fetch(new Request(url, {
		method: "GET",
		credentials: "same-origin",
		cache: "no-cache"
	})).then(function(response) {
		if (response.status === 200) {
			// this is a promise
			return response.json();
		}
		else {
			return Promise.reject();
		}
	});
}

function showErrorNotification() {
	return self.registration.showNotification("Unable To Retrieve Notification Data", {  
		body: "Something went wrong and we were unable to get the contents of this notification."
	});
}