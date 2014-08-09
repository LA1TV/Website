$(document).ready(function() {
	
	$("button[data-action='delete']").click(function() {
		if (confirm("Are you sure you want to delete this?")) {
			var id = $(this).attr("data-deleteid");
			var uri = $(this).attr("data-deleteuri");
			
			var data = {
				id: id,
				csrf_token: getCsrfToken()
			};
			
			var errorOccurred = function() {
				alert("Sorry there was an error deleting.");
			};
			
			jQuery.ajax({
				url: uri,
				cache: false,
				data: data,
				dataType: "json",
				timeout: 5000,
				type: "POST"
			}).done(function(data) {
				if (!data.success) {
					errorOccurred();
					return;
				}
				// success. reload page
				pageProtect.disable();
				window.location.reload();
			}).fail(function() {
				errorOccurred();
			});
		}
	});
	
});