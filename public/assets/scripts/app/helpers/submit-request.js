// create a virtual form with params and values and then submit it
define(["jquery", "../page-data", "lib/domReady!"], function($, PageData) {
	var submitRequest = function(method, action, data) {
		var $form = $("<form />").attr("method", method).attr("action", action).addClass("hidden");
		// add csrf token
		data.csrf_token = PageData.get("csrfToken");
		for (var key in data) {
			$el = $('<input />').attr("type", "hidden").attr("name", key).val(data[key]);
			$form.append($el);
		}
		$("body").append($form);
		$form.submit();
	};
	return submitRequest;
});