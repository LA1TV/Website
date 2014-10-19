define(["./page-data", "ga"], function(PageData, ga) {
	if (!PageData.get("gaEnabled")) {
		return;
	}
	
	ga('create', 'UA-43879336-5', 'auto');
	ga('send', 'pageview');
	
	function sendHeartbeat() {
		ga('send', 'event', 'Heartbeat', 'Beat', {'nonInteraction': 1});
		setTimeout(sendHeartbeat, 5*60*1000);
	}
	sendHeartbeat();
	
});