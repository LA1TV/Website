define(["jquery", "./page-data"], function($, PageData) {
	
	return function() {
		
		var interval = 240000;
		
		function ping() {
			$.ajax({
				url: PageData.get("baseUrl")+"/ajax/hello",
				timeout: 3000,
				dataType: "json",
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