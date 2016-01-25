define(["jquery", "./page-data", "./helpers/ajax-helpers"], function($, PageData, AjaxHelpers) {
	
	return function() {
		
		var interval = 240000;
		
		function ping() {
			$.ajax({
				url: PageData.get("baseUrl")+"/ajax/hello",
				timeout: 3000,
				dataType: "json",
				headers: AjaxHelpers.getHeaders(),
				data: {
					csrf_token: PageData.get("csrfToken")
				},
				cache: false,
				type: "POST"
			}).always(function() {
				setTimeout(ping, interval);
			});
		}
		setTimeout(ping, interval);
	};
});