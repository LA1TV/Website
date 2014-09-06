var baseUrl = null;
var assetsBaseUrl = null;

$(document).ready(function() {
	var $body = $("body");
	baseUrl = $body.attr("data-baseurl");
	assetsBaseUrl = $body.attr("data-assetsbaseurl");
});