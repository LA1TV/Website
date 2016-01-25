var PageData = require("./page-data");
var Logger = require("./logger");
var Promise = require("lib/es6-promise").Promise;

// by default don't load the lib
// if ga is enabled then this gets set to the loader
var gaLoader = null;

var queuedCallArgs = [];
var myGa = function() {
	// queue arguments so function can be called later when ready
	queuedCallArgs.push(arguments);
};

var enabled = PageData.get("gaEnabled");
if (enabled) {
	// when this promise resolves it will provide the gaLoder module
	gaLoader = new Promise(function(resolve) {
		// require asynchronously
		require(["external/ga"], function(gaLoader) {
			resolve(gaLoader);
		});	
	});
}
else {
	gaLoader = Promise.reject();
}

gaLoader.then(function(ga) {

	// myGa gets set to the google analytics function if it should be enabled, otherwise just a stub
	myGa = ga;
	myGa('create', 'UA-43879336-5', 'auto');
	myGa('send', 'pageview');
	
	function sendHeartbeat() {
		myGa('send', 'event', 'Heartbeat', 'Beat', {'nonInteraction': 1});
		setTimeout(sendHeartbeat, 5*60*1000);
	}
	sendHeartbeat();

	// now do any buffered calls which were made whilst were waiting for library
	// to download
	for (var i=0; i<queuedCallArgs.length; i++) {
		myGa.apply(this, queuedCallArgs[i]);
	}
	queuedCallArgs = [];
}).catch(function() {
	myGa = function(){};
	queuedCallArgs = [];
	if (enabled) {
		Logger.info("Google analytics script could not be loaded.")
	}
});


module.exports = {
	registerModulesLoadTime: function(site, timeTaken) {
		myGa('send', 'timing', site, 'RequireJS modules load time.', timeTaken);
	},
	registerPlayerEvent: function(action, playerType, contentId, playerTime) {
		if (action !== "play" && action !== "pause" && action !== "ended" && action !== "playing") {
			throw "Invalid action.";
		}
		else if (playerType !== "live" && playerType !== "vod") {
			throw "Invalid player type.";
		}
		myGa('send', 'event', playerType === "vod" ? "VOD Player" : "Live Player", action, "Content id: "+contentId, Math.round(playerTime));
	}
};
