define(["./page-data"], function(PageData) {
	
	var queuedCallArgs = [];
	var myGa = function() {
		// queue arguments so function can be called later when ready
		queuedCallArgs.push(arguments);
	};

	// require the google analytics library asynchronously to not hold up module which required this.
	// any analytics requests will be buffered and made when the library has loaded
	require(["optional!ga"], function(ga) {
		if (ga === null) {
			console.log("Google analytics will be disabled because it couldn't be loaded.");
		}
		
		var enabled = PageData.get("gaEnabled") && ga !== null;

		// myGa gets set to the google analytics function if it should be enabled, otherwise just a stub
		if (enabled) {
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
		}
		else {
			myGa = function(){};
		}
		queuedCallArgs = [];
	});
	
	return {
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
	
});