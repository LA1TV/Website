$(document).ready(function() {
	
	$("button[data-action='delete']").click(function() {
		if (confirm("Are you sure you want to delete this?")) {
			var id = $(this).attr("data-deleteid");
			var uri = $(this).attr("data-deleteuri");
			
			var data = {
				id: id
			};
			jQuery.ajax({
				url: uri,
				cache: false,
				data: data,
				dataType: "json",
				timeout: 5000,
				type: "POST"
			}).done(function(data) {
				// success. reload page
				pageProtect.disable();
				window.location.reload();
			}).fail(function() {
				alert("Sorry there was an error deleting.");
			});
		}
	});
	
});