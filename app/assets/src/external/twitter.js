var ScriptLoader = require("./script-loader");
var Promise = require("lib/es6-promise").Promise;

// a promise that will resolve with the twitter object if the script
// loads succesfully
module.exports = ScriptLoader.load("https://platform.twitter.com/widgets.js").then(function() {
	return Promise.resolve(window.twttr);
});