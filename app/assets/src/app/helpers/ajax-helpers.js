define(["../page-data"], function(PageData) {
	return {
		// get the headers that should be included with each request to the application
		getHeaders: function() {
			var headers = {};
			var sessionId = PageData.get("sessionId");
			if (sessionId !== null) {
				headers["X-Session-Id"] = sessionId;
			}
			return headers;
		}
	};
});