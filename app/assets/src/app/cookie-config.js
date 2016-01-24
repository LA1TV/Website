define(["./page-data"], function(PageData) {
	// https://github.com/carhartl/jquery-cookie
	return {
		path: "/",
		domain: PageData.get("cookieDomain"),
		secure: PageData.get("cookieSecure")
	};
});