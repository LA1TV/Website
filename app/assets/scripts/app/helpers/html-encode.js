define(["jquery"], function($) {
	return function(str) {
		return $("<div />").text(str).html();
	}
});