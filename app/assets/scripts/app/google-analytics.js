define(["./page-data", "ga"], function(PageData, ga) {
	
	if (!PageData.get("gaEnabled")) {
		return;
	}
	
	ga('create', 'UA-43879336-5', 'auto');
	ga('send', 'pageview');
});