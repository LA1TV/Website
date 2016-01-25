/// serverData is a webpack external and will be a raw js object containg the data
define(["jquery", "serverData"], function($, serverData) {
	return {
		get: function(key) {
			return serverData.hasOwnProperty(key) ? serverData[key] : null;
		}
	};
});