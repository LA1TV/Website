var getCsrfToken = null;

$(document).ready(function() {
	var csrfToken = null;
	
	getCsrfToken = function() {
		return csrfToken !== null ? csrfToken : csrfToken = $("body").attr("data-csrftoken");
	}
	
});