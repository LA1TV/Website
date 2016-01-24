var $ = require("jquery");
var SmartTime = require("../smart-time");
var PageData = require("../page-data");
var nl2br = require("../helpers/nl2br");
var e = require("../helpers/html-encode");
var AjaxHelpers = require("../helpers/ajax-helpers");
require("imports?jQuery=lib/jquery!lib/jquery.dateFormat");
require("./comments.css");

var CommentsComponent = function(getUri, postUri, deleteUri, canPostAsFacebookUser, canPostAsStation) {
		
	var self = this;
	
	this.getEl = function() {
		return $container;
	};
	
	this.destroy = function() {
		destroying = true;
		clearTimeout(retrieveCommentsTimerId);
		retrieveCommentsTimerId = null;
		for (var i=0; i<comments.length; i++) {
			var comment = comments[i];
			if (comment.smartTime !== null) {
				smartTime.destroy();
			}
		}
	};
	
	// contains all loaded comments in form {id, profilePicUri, postTime, name, msg, edited, permissionToDelete, deleted, deleting, $el, $deleteButton, smartTime}. $el is the reference to the dom element containing the comment
	// in order of id (which is same as time)
	var comments = [];
	var loadedAllComments = true; // set to true when all the comments up to the first one have been loaded. initialised to false in retrieveComments
	var retrieveCommentsTimerId = null;
	var commentIdToScrollTo = null;
	var onlyScrollIfFollowing = null;
	var postingComment = false; // true when a post is in progress
	var loadingMore = false; // set to true when older comments are being loaded.
	var destroying = false;
	
	var $container = $("<div />").addClass("comments");
	var $well = $("<div />").addClass("well well-sm");
	var $tableContainer = $("<div />").addClass("comments-table-container");
	var $table = $("<table />").addClass("comments-table table table-bordered table-hover");
	var $colGroup = $("<colgroup />").append($("<col />").attr("class", "col-1")).append($("<col />").attr("class", "col-2"));
	var $loadMoreRow = $("<tr />");
	var $loadMoreCol = $("<td />").addClass("load-more-col").attr("colspan", "2");
	var $loadMoreColButton = $("<button />").addClass("btn btn-info btn-sm btn-block").prop("type", "button");
	var $noCommentsRow = $("<tr />");
	var $noCommentsCol = $("<td />").addClass("no-comments-col").attr("colspan", "2");
	var $noCommentsMsg = $("<div />").addClass("no-comments-msg").text("There are no comments at the moment.");
	var $newCommentContainer = $("<div />").addClass("new-comment-container clearfix");
	var $loginMsg = $("<div />").addClass("login-msg").text("Please login to comment.");
	var $comment = $("<input />").attr("type", "comment").addClass("form-control").attr("placeholder", "Enter comment...");
	var $buttonsRow = $("<div />").addClass("buttons-row");
	var $postAsStationItem = $("<div />").addClass("item");
	var $postAsStationCheckboxContainer = $("<div />").addClass("checkbox");
	var $checkboxLabel = $("<label />");
	var $checkboxInput = $("<input />").attr("type", "checkbox");
	var $checkboxSpan = $("<span />").text(" Post as station.");
	var $postButtonItem = $("<div />").addClass("item");
	var $postButton = $("<button />").addClass("btn btn-primary btn-sm").prop("type", "button").text("Post");
	
	$well.append($tableContainer);
	$tableContainer.append($table);
	$table.append($colGroup);
	$table.append($noCommentsRow);
	$noCommentsRow.append($noCommentsCol);
	$noCommentsCol.append($noCommentsMsg);
	$table.append($loadMoreRow);
	$loadMoreRow.append($loadMoreCol);
	$loadMoreCol.append($loadMoreColButton);
	if (canPostAsFacebookUser || canPostAsStation) {
		$newCommentContainer.append($comment);
		$newCommentContainer.append($buttonsRow);
		if (canPostAsStation) {
			if (!canPostAsFacebookUser) {
				$checkboxInput.prop("checked", true).prop("disabled", true);
			}
			$buttonsRow.append($postAsStationItem);
			$postAsStationItem.append($postAsStationCheckboxContainer);
			$postAsStationCheckboxContainer.append($checkboxLabel);
			$checkboxLabel.append($checkboxInput);
			$checkboxLabel.append($checkboxSpan);
		}
		$buttonsRow.append($postButtonItem);
		$postButtonItem.append($postButton);
	}
	else {
		$newCommentContainer.append($loginMsg);
	}
	$well.append($newCommentContainer);
	$postButton.click(function() {
		postComment();
	});
	
	$comment.on("keyup change", function(e) {
		render(); // post button changes state depending on content of $comment
	});
	
	$loadMoreColButton.click(function() {
		loadingMore = true;
		render();
		retrieveComments(false);
	});
	
	// listen for enter key
	$comment.keyup(function(e) {
		if (e.which !== 13) {
			return;
		}
		e.preventDefault();
		if (getEnteredComment() !== "") {
			postComment();
		}
	});
	
	render();
	$container.append($well);
	retrieveCommentsTimerTask();
	
	function postComment() {
		if (postingComment) {
			return;
		}
	
		var msg = getEnteredComment();
		if (msg === "") {
			throw "postComment() should not be called if there is no message.";
		}
		else if (msg.length > 500) {
			alert("Your comment must be 500 characters or less.");
			return;
		}
		postCommentImpl(msg);
	}
	
	function postCommentImpl(msg) {
		postingComment = true;
		render();
		
		function doPost() {
			$.ajax(postUri, {
				cache: false,
				dataType: "json",
				headers: AjaxHelpers.getHeaders(),
				data: {
					csrf_token: PageData.get("csrfToken"),
					msg: msg,
					post_as_station: canPostAsStation && $checkboxInput.prop("checked") ? "1" : "0"
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					if (data.success) {
						$comment.val("");
						// scroll to the new comment when it exists
						scrollToCommentWithId(data.id, false);
						retrieveCommentsTimerTask();
						postingComment = false;
					}
					else {
						// failed because user has made too many comments recently.
						// keep trying every 8 seconds until the server allows it again
						setTimeout(doPost, 8000);
					}
				}
				else {
					alert("An error occurred when trying to post your comment. Please try again later.");
					postingComment = false;
				}
				render();
			});
		}
		
		doPost();
	}
	
	// delete comment
	function deleteComment(comment) {
		comment.deleting = true;
		render();
		$.ajax(deleteUri, {
			cache: false,
			dataType: "json",
			headers: AjaxHelpers.getHeaders(),
			data: {
				csrf_token: PageData.get("csrfToken"),
				id: comment.id
			},
			type: "POST"
		}).always(function(data, textStatus, jqXHR) {
			comment.deleting = false;
			if (jqXHR.status === 200 && data.success) {
				// mark as deleted so it will be removed from the dom in render()
				comment.deleted = true;
			}
			else {
				alert("Sorry the comment can not be deleted at the moment. Please try again later.");
			}
			render();
		});
	}
	
	function retrieveCommentsTimerTask() {
		if (destroying) {
			return;
		}
		
		if (retrieveCommentsTimerId !== null) {
			clearTimeout(retrieveCommentsTimerId);
			retrieveCommentsTimerId = null;
		}
		
		// if there are no comments loaded yet then request previous comments (which will request all previous comments from current time). otherwise request future ones.
		retrieveComments(comments.length > 0);
		retrieveCommentsTimerId = setTimeout(retrieveCommentsTimerTask, 8000);
	}
	
	// updates the comments array
	function retrieveComments(loadLaterComments) {
		
		var id = null;
		var initialLoad = false;
		if (comments.length === 0 && loadLaterComments) {
			throw "You cannot load later comments when there are no comments loaded.";
		}
		else if (loadLaterComments) {
			id = comments[comments.length-1].id;
		}
		else {
			id = comments.length > 0 ? comments[0].id : -1;
			if (comments.length === 0) {
				initialLoad = true;
			}
		}

		$.ajax(getUri, {
			cache: false,
			dataType: "json",
			headers: AjaxHelpers.getHeaders(),
			data: {
				csrf_token: PageData.get("csrfToken"),
				id: id,
				load_later_comments: loadLaterComments ? "1" : "0"
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
						escapedMsg: comment.escapedMsg,
						edited: comment.edited,
						permissionToDelete: comment.permissionToDelete,
						deleted: false,
						deleting: false,
						$el: null, // to contain the dom el
						$deleteButton: null,
						smartTime: null
					});
				}
				
				if (loadLaterComments) {
					// if there there are concurrent requests then this makes sure there are no duplicate additions
					var fromId = comments[comments.length-1].id;
					for (var i=0; i<newComments.length; i++) {
						var newComment = newComments[i];
						if (newComment.id > fromId) {
							comments.push(newComment);
						}
					}
					
					if (newComments.length > 0) {
						scrollToCommentWithId(comments[comments.length-1].id, true);
					}
				}
				else {
					loadedAllComments = !data.more;
					// if there there are concurrent requests then this makes sure there are no duplicate additions
					var fromId = comments.length > 0 ? comments[0].id : null;
					for (var i=newComments.length-1; i>=0; i--) {
						var newComment = newComments[i];
						if (fromId === null || newComment.id < fromId) {
							comments.unshift(newComment);
						}
					}
					
					if (initialLoad && comments.length > 0) {
						scrollToCommentWithId(comments[comments.length-1].id, false);
					}
					loadingMore = false;
				}
				render();
			}
			else {
				if (!loadLaterComments && loadingMore) {
					loadingMore = false;
					alert("An error occurred when trying to load earlier comments. Please try again later.");
					render();
				}
			}
		});
	}
	
	function getEnteredComment() {
		return $.trim($comment.val());
	}

	function render() {
		
		var following = $table.outerHeight(true) < $tableContainer.innerHeight() || $tableContainer.scrollTop() + $tableContainer.innerHeight() >= $table.outerHeight(true)-60;
		
		$noCommentsRow.css("display", getNumberOfActiveComments() > 0 ? "none" : "table-row");
		$loadMoreRow.css("display", loadedAllComments ? "none" : "table-row");
		$loadMoreColButton.prop("disabled", loadingMore);
		$loadMoreColButton.text(!loadingMore ? "Load Earlier Comments" : "Loading...");
		$comment.prop("disabled", postingComment);
		$postButton.prop("disabled", postingComment || getEnteredComment() === "");
		$checkboxInput.prop("disabled", !canPostAsFacebookUser || postingComment);
		
		for (var i=0; i<comments.length; i++) {
			var comment = comments[i];
			if (comment.deleted) {
				if (comment.$el !== null) {
					comment.smartTime.destroy();
					comment.$el.remove();
					comment.$el = null;
					comment.$deleteButton = null;
				}
			}
			else if (comment.$el === null) {
				var commentToInsertAfter = getPreviousCommentWithEl(comment.id);
				comment.$el = buildCommentEl(comment);
				if (commentToInsertAfter === null) {
					comment.$el.insertAfter($loadMoreRow);
				}
				else {
					comment.$el.insertAfter(commentToInsertAfter.$el);
				}
			}
			
			if (comment.$deleteButton !== null) {
				comment.$deleteButton.prop("disabled", comment.deleting);
			}
		}
		
		// see if there's a queued comment to scroll to
		if (commentIdToScrollTo !== null) {
			var comment = getComment(commentIdToScrollTo);
			if (comment !== null) {
				// comment exists now
				if (!onlyScrollIfFollowing || following) {
					var $el = comment.$el;
					$tableContainer.animate({
						scrollTop: $el.offset().top - $tableContainer.offset().top + $tableContainer.scrollTop()
					});
					commentIdToScrollTo = null;
				}
			}
		}
	}
	
	function getPreviousCommentWithEl(startId) {
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
	
	function getComment(commentId) {
		for (var i=0; i<comments.length; i++) {
			var a = comments[i];
			if (a.id === commentId) {
				return a;
			}
		}
		return null;
	}
	
	function getNumberOfActiveComments() {
		var num = 0;
		for (var i=0; i<comments.length; i++) {
			var comment = comments[i];
			if (!comment.deleted) {
				num++;
			}
		}
		return num;
	}
	
	// queued scroll to comment on next render. If the id doesn't exist it will wait until it does and then scroll to it.
	// if onlyIfFollowing is true the scroll will only occur if the comments list is scrolled all the way to the bottom
	function scrollToCommentWithId(commentId, onlyIfFollowing) {
		if (commentIdToScrollTo === commentId && !onlyScrollIfFollowing) {
			// if the comment is already queued and it's set to always scroll then don't do anything.
			// something that's set to scroll should take precedence over the same comment getting set again but with scroll only if following set.
			return;
		}
		commentIdToScrollTo = commentId;
		onlyScrollIfFollowing = onlyIfFollowing;
		render();
	}
	
	function buildCommentEl(comment) {
		var $el = $("<tr />");
		var $profilePicCol = $("<td />").addClass("profile-pic-col");
		var $profilePicContainer = $("<div />").addClass("profile-pic-container");
		var $profilePicImg = $("<img />").attr("src", comment.profilePicUri);
		var $commentBoxCol = $("<td />").addClass("comment-box-col");
		var $commentBox = $("<div />").addClass("comment-box clearfix");
		var $buttonsContainer = $("<div />").addClass("buttons-container");
		var $timeItem = $("<div />").addClass("item");
		var $time = $("<span />").addClass("time").attr("title", $.format.date(comment.postTime*1000, "HH:mm on D MMM yyyy"));
		var $item = $("<div />").addClass("item");
		var $deleteButton = $("<button />").addClass("remove-btn btn btn-danger btn-xs").prop("type", "button").html("&times;");
		var $topRow = $("<div />").addClass("top-row");
		var $name = $("<span />").addClass("name").text(comment.name);
		var $comment = $("<div />").addClass("comment").html(nl2br(comment.escapedMsg));
		
		$el.append($profilePicCol);
		$profilePicCol.append($profilePicContainer);
		$profilePicContainer.append($profilePicImg);
		$el.append($commentBoxCol);
		$commentBoxCol.append($commentBox);
		$commentBox.append($buttonsContainer);
		$buttonsContainer.append($timeItem);
		$timeItem.append($time);
		if (comment.permissionToDelete) {
			$buttonsContainer.append($item);
			$item.append($deleteButton);
			
			$deleteButton.click(function() {
				if (confirm("Are you sure you want to delete this comment?")) {
					deleteComment(comment);
				}
			});
			comment.$deleteButton = $deleteButton;
		}
		$commentBox.append($topRow);
		$topRow.append($name);
		$commentBox.append($comment);
		comment.smartTime = new SmartTime($time, comment.postTime*1000);
		return $el;
	}
	
};
module.exports = CommentsComponent;