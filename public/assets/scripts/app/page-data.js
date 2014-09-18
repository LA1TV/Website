define(["jquery", "lib/domReady!"], function($) {
	
	var data = jQuery.parseJSON($("body").attr("data-pagedata"));
	
	return {
		get: function(key) {
			return data.hasOwnProperty(key) ? data[key] : null;
		}
	};
});