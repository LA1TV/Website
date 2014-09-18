// generate query string with any attributes in uri still set unless overridden in attrs object
define(["./uri-params"], function(UriParams) {

	return function(attrs) {
		
		params = UriParams.get();
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