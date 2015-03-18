// generate query string with any attributes in uri still set unless overridden in attrs object
define(["./uri-params"], function(UriParams) {

	// if uri isn't specified then the current uri will be used
	return function(attrs, uri) {
		
		params = UriParams.get(uri);
		jQuery.extend(params, attrs);
		// params are now the new params we want
		var uri = "?";
		var hadFirst = false;
		for (var key in params) {
			if (hadFirst) {
				uri += "&";
			}
			uri += encodeURIComponent(key)+"="+ encodeURIComponent(params[key]);
			hadFirst = true;
		}
		return uri;
	};
});