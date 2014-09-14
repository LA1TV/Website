$(document).ready(function() {

	$(".comments").each(function() {
	
		var mediaItemId = parseInt($(this).attr("data-mediaitemid"));
		
		// contains all loaded comments in form {id, profilePicUri, postTime, name, msg, edited, $el}. $el is the reference to the dom element containing the comment
		// in order of id (which is same as time)
		var comments = [];
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
					
					var newComments = [];
					for (var i=0; i<data.comments.length; i++) {
						var comment = data.comments[i];
						newComments.push({
							id: comment.id,
							profilePicUri: comment.profilePicUri,
							postTime: comment.postTime,
							name: comment.name,
							msg: comment.msg,
							edited: comment.edited,
							$el: null // to contain the dom el
						});
					}
					
					if (loadLaterComments) {
						Array.prototype.push.apply(comments, newComments);
					}
					else {
						Array.prototype.unshift.apply(comments, newComments);
					}
					render();
				}
			});
		}
		
		retrieveComments(-1, false);
	
		function render() {
			for (var i=0; i<comments.length; i++) {
				var comment = comments[i];
				if (comment.$el === null) {
					
				}
			}
		}
	});

});