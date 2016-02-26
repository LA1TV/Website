var ScriptLoader = require("./script-loader");
var PageData = require("app/page-data");
var Promise = require("lib/es6-promise").Promise;
var Logger = require("app/logger");

var fbAppId = PageData.get("fbAppId");
if (!fbAppId) {
	Logger.warn("Can not load facebook script as missing app ID.");
	module.exports = Promise.reject();
}
else {

	// a promise that will resolve with the facebook object if the script
	// loads succesfully
	module.exports = new ScriptLoader.load("//connect.facebook.net/en_GB/sdk.js").then(function() {
		// init
		// https://developers.facebook.com/docs/javascript/quickstart/v2.5
		window.FB.init({
			appId: fbAppId,
			xfbml: false,
			version: 'v2.5'
		});
		return Promise.resolve(window.FB);
	});
}