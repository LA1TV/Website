$(document).ready(function() {

	$(".comments").each(function() {
	
		var mediaItemId = parseInt($(this).attr("data-mediaitemid"));
		
		var comments = []; // contains all loaded comments in form {id, profilePicUri, postTime, name, msg, edited, el}. el is the reference to the dom element containing the comment
		var loadedAllComments = false; // set to true when all the comments up to the first one have been loaded.

		
		// if loadLaterComments is true then comments after the entered id will be retrieved, otherwise comments before it will be retrieved.
		// updates the comments array
		function retrieveComments(id, loadLaterComments) {
			jQuery.ajax(baseUrl+"/player/comments/"+mediaItemId, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: getCsrfToken(),
					id: id,
					load_later_comments: loadLaterComments
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					console.log(data);
				}
			});
		}
		
		retrieveComments(-1, false);
	
		function render() {
			
		}
	});

});