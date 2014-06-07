$(document).ready(function() {
	
	$("button[data-action='delete']").click(function() {
		if (confirm("Are you sure you want to delete this?")) {
			pageProtect.disable();
			// create form and submit it
		}
	});
	
});