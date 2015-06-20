define(["./page-data", "optional!ga"], function(PageData, ga) {
	
	if (ga === null) {
		console.log("Google analytics will be disabled because it couldn't be loaded.");
	}
	
	var enabled = PageData.get("gaEnabled") && ga !== null;
	// myGa gets set to the google analytics function if it should be enabled, otherwise just a stub
	var myGa = enabled ? ga : function(){};
	
	if (enabled) {
		myGa('create', 'UA-43879336-5', 'auto');
		myGa('send', 'pageview');
		
		function sendHeartbeat() {
			myGa('send', 'event', 'Heartbeat', 'Beat', {'nonInteraction': 1});
			setTimeout(sendHeartbeat, 5*60*1000);
		}
		sendHeartbeat();
	}

	
	return {
		registerModulesLoadTime: function(site, timeTaken) {
			myGa('send', 'timing', site, 'RequireJS modules load time.', timeTaken);
		},
		registerPlayerEvent: function(action, playerType, mediaItemId, playerTime) {
			if (action !== "play" && action !== "pause" && action !== "ended" && action !== "playing") {
				throw "Invalid action.";
			}
			else if (playerType !== "live" && playerType !== "vod") {
				throw "Invalid player type.";
			}
			myGa('send', 'event', playerType === "vod" ? "VOD Player" : "Live Player", action, "Media item id: "+mediaItemId, Math.round(playerTime));
		}
	};
	
});