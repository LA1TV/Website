var PageData = require("./page-data");
var logger = require("app/logger");
var Promise = require("lib/es6-promise").Promise;

var serviceWorkerInstalledResolve = null;
var serviceWorkerInstalledReject = null;
var serviceWorkerInstalled = new Promise(function(resolve, reject) {
	serviceWorkerInstalledResolve = resolve;
	serviceWorkerInstalledReject = reject;
});

install().then(function() {
	// installed
	logger.debug("Service worker installed.");
	serviceWorkerInstalledResolve();
}).catch(function() {
	// failed to install
	logger.debug("Service worker failed to install.");
	serviceWorkerInstalledReject();
});

function serviceWorkerSupported() {
	return 'serviceWorker' in navigator;
}

function pushSupported() {
	if (!serviceWorkerSupported()) {
		return false;
	}
	// Check if push messaging is supported  
	if (!('PushManager' in window)) {  
			return false;
	}
	return true;
}

// same as navigator.serviceWorker.ready except it will reject if the service worker fails to install.
// navigator.serviceWorker.ready itself never rejects
function serviceWorkerReady() {
	return new Promise(function(resolve, reject) {
		serviceWorkerInstalled.then(function() {
			navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
				resolve(serviceWorkerRegistration);
			});
		}).catch(function() {
			reject();
		})
	});
}

// installs/updates the service worker
function install() {
	if (!serviceWorkerSupported()) {
		logger.debug("Service workers not supported.");
		return Promise.reject();
	}
	
	// Note this will fail if on an insecure origin, e.g. in development environment
	// to use in development run chrome with the flags "chrome --user-data-dir=/tmp/chromedev --unsafely-treat-insecure-origin-as-secure=http://www.la1tv.co.uk.local:8000"
	return navigator.serviceWorker.register(PageData.get("serviceWorkerUrl"));
}

function getPushSubscription() {
	return new Promise(function(resolve, reject) {
		if (!pushSupported()) {  
  			reject();
		}

		serviceWorkerReady().then(function(serviceWorkerRegistration) {
  			serviceWorkerRegistration.pushManager.getSubscription().then(function(subscription) {  
				subscription ? resolve(subscription) : reject();
	 		}).catch(function() {
				reject();
			});
		}).catch(function() {
			reject();
		});
	});
}

function subscribeToPush() {
	return new Promise(function(resolve, reject) {
		logger.debug("Making push subscription.");
		if (!pushSupported()) {
			logger.debug("Push not supported.");
  			reject();
		}

		serviceWorkerReady().then(function(serviceWorkerRegistration) {
			// userVisibleOnly as true means each push event triggered must trigger a notification in chrome (https://goo.gl/yqv4Q4)
			// This is required for push to work in chrome, and the only use at the moment is triggering notifications so this is fine
			serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly: true}).then(function(subscription) {  
				if (subscription) {
					logger.debug("Got push subscription.");
					resolve(subscription);
				}
				else {
					logger.debug("Failed to subscribe to push.");
					reject();
				}
			}).catch(function() {
				logger.debug("Failed to subscribe to push. Exception was thrown.");
				reject();
			});
		}).catch(function() {
			logger.debug("Could not subscribe to push because service worker never became ready.");
			reject();
		});
	});
}

function unsubscribeFromPush() {
	return new Promise(function(resolve, reject) {
		logger.debug("Unsubscribing push subscription.");
		if (!pushSupported()) {
			logger.debug("Push not supported!");
  			reject();
		}

		serviceWorkerReady().then(function(serviceWorkerRegistration) {
			serviceWorkerRegistration.pushManager.unsubscribe().then(function(success) {  
				if (success) {
					logger.debug("Unsubscribed push subscription.");
					resolve();
				}
				else {
					logger.debug("Failed to unsubscribe push subscription.");
					reject();
				}
			}).catch(function() {
				logger.debug("Failed to unsubscribe push subscription. Exception was thrown.");
				reject();
			});
		}).catch(function() {
			logger.debug("Could not unsubscribe from push subscription because service worker never became ready.");
			reject();
		});
	});
}

function postMessage(message) {
	// This wraps the message posting/response in a promise, which will resolve if the response doesn't
	// contain an error, and reject with the error if it does.
	return new Promise(function(resolve, reject) {
		if (!('serviceWorker' in navigator)) {
			reject();
			return;
		}

		serviceWorkerReady().then(function(serviceWorkerRegistration) {
			var messageChannel = new MessageChannel();
			messageChannel.port1.onmessage = function(event) {
				if (event.data.error) {
					reject(event.data.error);
				} else {
					resolve(event.data);
				}
			};

			// This sends the message data as well as transferring messageChannel.port2 to the service worker.
			// The service worker can then use the transferred port to reply via postMessage(), which
			// will in turn trigger the onmessage handler on messageChannel.port1.
			// See https://html.spec.whatwg.org/multipage/workers.html#dom-worker-postmessage
			navigator.serviceWorker.controller.postMessage(message, [messageChannel.port2]);
		}).catch(function(e) {
			reject();
		});
	});
}

module.exports = {
	postMessage: postMessage,
	getPushSubscription: getPushSubscription,
	subscribeToPush: subscribeToPush,
	unsubscribeFromPush: unsubscribeFromPush
};