var ScriptLoader = require("./script-loader");
var Promise = require("lib/es6-promise").Promise;

// a promise that will resolve if the script
// loads succesfully
module.exports = ScriptLoader.load("//api.peer5.com/peer5.clappr.plugin.js").then(function() {
	// TODO resolve with the clappr plugin
	return Promise.resolve();
});