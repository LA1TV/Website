var baseUrl = null;
var assetsBaseUrl = null;
var loggedIn = null;

$(document).ready(function() {
	var $body = $("body");
	baseUrl = $body.attr("data-baseurl");
	assetsBaseUrl = $body.attr("data-assetsbaseurl");
	loggedIn = $body.attr("data-loggedin") === "1";
});