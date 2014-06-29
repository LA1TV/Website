// create a virtual form with params and values and then submit it

var submitVirtualForm = null;

$(document).ready(function() {

	submitVirtualForm = function(method, action, data) {
		var $form = $("<form />").attr("method", method).attr("action", action).addClass("hidden");
		// add csrf token
		data.csrf_token = getCsrfToken();
		for (var key in data) {
			$el = $('<input />').attr("type", "hidden").attr("name", key).val(data[key]);
			$form.append($el);
		}
		$("body").append($form);
		$form.submit();
	};
});