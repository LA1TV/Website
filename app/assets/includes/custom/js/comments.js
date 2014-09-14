$(document).ready(function() {

	$(".comments").each(function() {
	
		var mediaItemId = parseInt($(this).attr("data-mediaitemid"));
		
		// contains all loaded comments in form {id, profilePicUri, postTime, name, msg, edited, $el}. $el is the reference to the dom element containing the comment
		// in order of id (which is same as time)
		var comments = [];
		var loadedAllComments = false; // set to true when all the comments up to the first one have been loaded.
		
		var $container = $(this).first();
		var $well = $("<div />").addClass("well well-sm");
		var $table = $("<table />").addClass("comments-table table table-bordered table-hover");
		var $loadMoreRow = $("<tr />");
		var $loadMoreCol = $("<td />").addClass("load-more-col").attr("colspan", "2");
		var $loadMoreColButton = $("<button />").addClass("btn btn-info btn-sm btn-block").prop("type", "button").text("Load More");
		var $newCommentContainer = $("<div />").addClass("new-comment-container clearfix");
		var $comment = $("<input />").prop("type", "comment").addClass("form-control").attr("placeholder", "Enter comment...");
		var $buttonsRow = $("<div />").addClass("buttons-row");
		var $postAsStationItem = $("<div />").addClass("item");
		var $postAsStationCheckboxContainer = $("<div />").addClass("checkbox");
		var $checkboxLabel = $("<label />");
		var $checkboxInput = $("<input />").prop("type", "checkbox");
		var $checkboxSpan = $("<span />").text(" Post as station.");
		var $postButtonItem = $("<div />").addClass("item");
		var $postButton = $("<button />").addClass("btn btn-primary btn-sm").prop("type", "button").text("Post");
		
		$well.append($table);
		$table.append($loadMoreRow);
		$loadMoreRow.append($loadMoreCol);
		$loadMoreCol.append($loadMoreColButton);
		$newCommentContainer.append($comment);
		$newCommentContainer.append($buttonsRow);
		$well.append($newCommentContainer);
		$newCommentContainer.append($buttonsRow);
		$buttonsRow.append($postAsStationItem);
		$postAsStationItem.append($postAsStationCheckboxContainer);
		$postAsStationCheckboxContainer.append($checkboxLabel);
		$checkboxLabel.append($checkboxInput);
		$checkboxLabel.append($checkboxSpan);
		$buttonsRow.append($postButtonItem);
		$postButtonItem.append($postButton);
		
		$container.append($well);
		
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
						if (!data.more) {
							loadedAllComments = true;
						}
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
					var commentToInsertAfter = getNextCommentWithEl(comment.id);
					comment.$el = buildCommentEl(comment);
					if (commentToInsertAfter === null) {
						comment.$el.insertAfter($loadMoreRow);
					}
					else {
						comment.$el.insertAfter(commentToInsertAfter.$el);
					}
				}
			}
		}
		
		function getNextCommentWithEl(startId) {
			var comment = null;
			for (var i=0; i<comments.length; i++) {
				var a = comments[i];
				if (a.$el !== null) {
					comment = a;
				}
				if (a.id === startId) {
					break;
				}
			}
			return comment;
		}
		
		function buildCommentEl(comment) {
			var $el = $("<tr />");
			var $profilePicCol = $("<td />").addClass("profile-pic-col");
			var $profilePicImg = $("<img />").attr("src", comment.profilePicUri);
			var $commentBoxCol = $("<td />").addClass("comment-box-col");
			var $commentBox = $("<div />").addClass("comment-box");
			var $buttonsContainer = $("<div />").addClass("buttons-container");
			var $item = $("<div />").addClass("item");
			var $button = $("<button />").addClass("remove-btn btn btn-danger btn-xs").prop("type", "button").html("&times;");
			var $topRow = $("<div />").addClass("top-row");
			var $name = $("<span />").addClass("name").text(comment.name+" ");
			var $time = $("<span />").addClass("time").text("5 minutes ago");
			var $comment = $("<div />").addClass("comment").html(nl2br(e(comment.msg)));
			
			$el.append($profilePicCol);
			$profilePicCol.append($profilePicImg);
			$el.append($commentBoxCol);
			$commentBoxCol.append($commentBox);
			$commentBox.append($buttonsContainer);
			$buttonsContainer.append($item);
			$item.append($button);
			$commentBox.append($topRow);
			$topRow.append($name);
			$topRow.append($time);
			$commentBox.append($comment);
			return $el;
		}
		
	});

});