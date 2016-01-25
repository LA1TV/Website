// generate query string with any attributes in uri still set unless overridden in attrs object
var $ = require("jquery");
var UriParams = require("./uri-params");
	
// if uri isn't specified then the current uri will be used
module.exports = function(attrs, uri) {
	
	params = UriParams.get(uri);
	$.extend(params, attrs);
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