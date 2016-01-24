var ScriptLoader = require("./script-loader");
var Promise = require("lib/es6-promise").Promise;

// a promise that will resolve with the twitter object if the script
// loads succesfully
module.exports = new ScriptLoader.load("https://www.google-analytics.com/analytics.js").then(function() {
	return Promise.resolve(window.ga);
});