define([
	"jquery",
	"lib/tether",
	"lib/domReady!"
], function($, Tether) {

	$(".navbar .search-btn").first().each(function() {

		var self = this;
		var $self = $(self);
		var $body = $('body');

		var $searchDialog = null;
		var $searchInput = null;
		var $resultsContainer = null;

		var visible = false;
		var $results = [];
		var resultIndexUnderCursor = null;
		var resultIndexWithArrowKeys = null;
		var chosenResultIndex = null;

		$body.click(function(e) {
			if ($(e.target).closest($self).length === 0 && $(e.target).closest($searchDialog).length === 0) {
				// a click outside of the search button or dialog
				hide();
			}
		});

		$(document).keyup(function(e) {
			if (e.keyCode === 27) {
				// escape key pushed
				hide();
			}
		});

		$(this).click(function() {
			toggle();
		});

		function toggle() {
			visible ? hide() : show();
		}

		function show() {
			if (visible) {
				return;
			}
			visible = true;
			initSearchDialog();
			$searchDialog.removeClass("hidden");
			$searchInput.focus();
		}

		function hide() {
			if (!visible) {
				return;
			}
			visible = false;
			$searchDialog.addClass("hidden");
		}

		function initSearchDialog() {
			if (!$searchDialog) {
				$searchDialog = $("<div />").addClass("search-dialog");
				
				var $inputContainer = $("<div />").addClass("search-input-container");
				$searchInput = $("<input />").addClass("form-control search-input").attr("autocomplete", "off").attr("placeholder", "Search...");
				$inputContainer.append($searchInput);
				$searchDialog.append($inputContainer);
				$resultsContainer = $("<div />").addClass("results-container");
				$searchDialog.append($resultsContainer);

				$searchDialog.keydown(function(e) {
					if (e.keyCode === 38) {
						// up
						e.preventDefault();
						if (resultIndexWithArrowKeys === null && resultIndexUnderCursor !== null) {
							// start from the item that is currently under the cursor
							resultIndexWithArrowKeys = resultIndexUnderCursor;
						}
						if (resultIndexWithArrowKeys === null) {
							return;
						}
						else if (resultIndexWithArrowKeys > 0) {
							resultIndexWithArrowKeys--;
							updateChosenResult();
							scrollResultIntoView($results[resultIndexWithArrowKeys]);
						}
					}
					else if (e.keyCode === 40) {
						// down
						e.preventDefault();
						if (resultIndexWithArrowKeys === null && resultIndexUnderCursor !== null) {
							// start from the item that is currently under the cursor
							resultIndexWithArrowKeys = resultIndexUnderCursor;
						}
						if (resultIndexWithArrowKeys === null) {
							resultIndexWithArrowKeys = 0;
						}
						else if (resultIndexWithArrowKeys < $results.length-1) {
							resultIndexWithArrowKeys++;
						}
						updateChosenResult();
						scrollResultIntoView($results[resultIndexWithArrowKeys]);
					}
				});


				// Temporary
				for (var i=0; i<20; i++) {
					var $result = buildResult();
					(function(resultIndex) {
						$result.hover(function() {
							resultIndexUnderCursor = resultIndex;
							resultIndexWithArrowKeys = null;
							updateChosenResult();
						}, function() {
							resultIndexUnderCursor = null;
							updateChosenResult();
						});
					})(i);
					$results.push($result);
					$resultsContainer.append($result);
				}

				$body.append($searchDialog);
				new Tether({
					element: $searchDialog,
					target: $self,
					attachment: 'top right',
					targetAttachment: 'bottom right'
				});
			}
			return $searchDialog;
		}

		function buildResult() {
			var $result = $("<div />").addClass("search-result");
			var $thumb = $("<div />").addClass("theThumbnail").css("background-image", 'url("http://www.la1tv.co.uk.local:8000/assets/img/default-cover.jpg")');
			var $info = $("<div />").addClass("info");
			var $title = $("<div />").addClass("title").text("Title");
			var $description = $("<div />").addClass("description").text("Blah blah blah Blah blah blah Blah blah blah Blah blah blah Blah blah blah Blah blah blah");
			$result.append($thumb);
			$info.append($title);
			$info.append($description);
			$result.append($info);
			return $result;
		}

		function updateChosenResult() {
			if (chosenResultIndex !== null) {
				$results[chosenResultIndex].removeClass("chosen");
			}
			if (resultIndexWithArrowKeys !== null) {
				chosenResultIndex = resultIndexWithArrowKeys;
			}
			else {
				chosenResultIndex = resultIndexUnderCursor;
			}
			if (chosenResultIndex !== null) {
				$results[chosenResultIndex].addClass("chosen");	
			}
		}

		function scrollResultIntoView($result) {
			// scroll to make sure result is visible
			var containerHeight = $resultsContainer.height();
			var containerTop = $resultsContainer.scrollTop();
			var containerBottom = containerTop + containerHeight;
			var resultHeight = $result.height();
			var resultTop = ($result.offset().top - $resultsContainer.offset().top) + containerTop;
			var resultBottom = resultTop + resultHeight;
			var resultVisible = resultTop >= containerTop && resultBottom <= containerBottom;
			if (resultVisible) {
				// already visible
				return;
			}

			if (resultTop < containerTop) {
				$resultsContainer.scrollTop(resultTop);
			}
			else {
				$resultsContainer.scrollTop(resultTop - containerHeight + resultHeight);
			}
		}
	});
});