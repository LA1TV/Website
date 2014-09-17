var getCsrfToken = null;

$(document).ready(function() {
	
	getCsrfToken = function() {
		return csrfToken;
	}
	
});