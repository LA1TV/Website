var ScriptLoader = require("./script-loader");
var PageData = require("app/page-data");
var Promise = require("lib/es6-promise").Promise;
var Logger = require("app/logger");


var apiKey = PageData.get("peer5ApiKey");
if (!apiKey) {
	Logger.warn("Can not load peer5 script as missing api key.");
	module.exports = Promise.reject();
}
else {
	// a promise that will resolve if the script
	// loads succesfully
	module.exports = ScriptLoader.load("//api.peer5.com/peer5.js?id="+encodeURIComponent(apiKey)).then(function() {
		return Promise.resolve(window.peer5);
	});
}