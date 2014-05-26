// handles attaching jquery.fileupload to all .ajax-upload

$(document).ready(function() {
	
	$(".ajax-upload").each(function() {
	
		// after file uploaded it's id should be stored in hidden form element with name-id
		var name = $(this).attr("data-name");
		
		// the reference to the hidden form element where the file id should be placed
		var $idInput = $(this).closest("form").find('[name="'+name+'-id"]').first();
		
		// the generated <input type="file">
		var $fileInput = $('<input />').prop("type", "file").addClass("hidden");
		
		// the upload button
		var $uploadBtn = $('<button />').prop("type", "button").addClass("btn").addClass("btn-xs").addClass("btn-info").text("Upload");
		
		$(this).append($fileInput);
		$(this).append($uploadBtn);
	});
	
});