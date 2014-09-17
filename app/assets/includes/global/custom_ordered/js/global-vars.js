// creates global variables from data-pagedata on the body tag

$(document).ready(function() {
	
	var data = jQuery.parseJSON($("body").attr("data-pagedata"));
	for (var i in data) {
		window[i] = data[i];
	}
});