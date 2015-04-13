define(["jquery", "./page-protect", "./page-data", "./helpers/ajax-helpers", "lib/domReady!"], function($, PageProtect, PageData, AjaxHelpers) {
	
	$("button[data-action='delete']").click(function() {
		if (confirm("Are you sure you want to delete this?")) {
			var id = $(this).attr("data-deleteid");
			var uri = $(this).attr("data-deleteuri");
			
			var data = {
				id: id,
				csrf_token: PageData.get("csrfToken")
			};
			
			var errorOccurred = function(msg) {
				alert(!msg ? "Sorry there was an error deleting." : msg);
			};
			
			jQuery.ajax({
				url: uri,
				cache: false,
				data: data,
				dataType: "json",
				headers: AjaxHelpers.getHeaders(),
				timeout: 5000,
				type: "POST"
			}).done(function(data, textStatus, jqXHR) {
				if (jqXHR.status !== 200) {
					errorOccurred();
					return;
				}
				else if (!data.success) {
					errorOccurred(data.hasOwnProperty("msg") ? data.msg : null);
					return;
				}
				// success. reload page
				PageProtect.disable();
				window.location.reload();
			}).fail(function() {
				errorOccurred();
			});
		}
	});
	
});