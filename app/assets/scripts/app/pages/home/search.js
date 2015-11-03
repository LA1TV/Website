define([
	"jquery",
	"lib/tether",
	"../../helpers/ajax-helpers",
	"../../page-data",
	"lib/domReady!"
], function($, Tether, AjaxHelpers, PageData) {

	$(".navbar .search-btn").first().each(function() {

		var self = this;
		var $self = $(self);
		var $body = $('body');

		var $searchDialog = null;
		var $searchInput = null;
		var $resultsContainer = null;

		var searchQueryUri = $self.attr("data-search-query-uri");
		var pendingQueryTimeoutId = null;
		var pendingQueryDelay = 300;
		var currentTerm = "";
		var queryXhr = null;
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
			cancelPendingQuery();
			cancelQuery();
			$searchInput.val("");
			$results = [];
			currentTerm = "";
			renderResults();
		}

		function initSearchDialog() {
			if (!$searchDialog) {
				$searchDialog = $("<div />").addClass("search-dialog");
				
				var $inputContainer = $("<div />").addClass("search-input-container");
				$searchInput = $("<input />").addClass("search-input").attr("autocomplete", "off").attr("placeholder", "Search...");
				$inputContainer.append($searchInput);
				$searchDialog.append($inputContainer);
				$resultsContainer = $("<div />").addClass("results-container");
				$searchDialog.append($resultsContainer);

				$searchInput.keyup(function() {
					prepareQuery($searchInput.val());
				});
				$searchInput.change(function() {
					submitQuery($searchInput.val());
				});

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

		function buildResult(result) {
			var $result = $("<div />").addClass("search-result");
			var $thumb = $("<div />").addClass("theThumbnail").css("background-image", 'url("'+result.thumbnailUri+'")');
			var $info = $("<div />").addClass("info");
			var $title = $("<div />").addClass("title").text(result.title);
			var $description = $("<div />").addClass("description").text(result.description);
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

		// the query will be made after a short delay, providing this isn't called againw tih
		// an updated term
		function prepareQuery(term) {
			cancelPendingQuery();
			pendingQueryTimeoutId = setTimeout(function() {
				pendingQueryTimeoutId = null;
				submitQuery(term);
			}, pendingQueryDelay);
		}

		function cancelPendingQuery() {
			if (pendingQueryTimeoutId !== null) {
				clearTimeout(pendingQueryTimeoutId);
				pendingQueryTimeoutId = null;
			}
		}

		function submitQuery(term) {
			// abort a previous query if there is one
			cancelQuery();
			cancelPendingQuery();

			queryXhr = jQuery.ajax(searchQueryUri, {
				cache: false,
				dataType: "json",
				headers: AjaxHelpers.getHeaders(),
				data: {
					csrf_token: PageData.get("csrfToken"),
					term: term
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				queryXhr = null;

				if (jqXHR.status === 200) {
					var results = data.results;
					onResults(results);
				}
			});

			function onResults(results) {
				$results = [];
				currentTerm = term;
				for(var i=0; i<results.length; i++) {
					var result = results[i];
					var $result = buildResult(result);
					$results.push($result);
				}
				renderResults();
			}
		}

		function cancelQuery() {
			if (queryXhr) {
				// abort a previous query if there is one
				queryXhr.abort();
			}
		}

		function renderResults() {
			$resultsContainer.empty();

			if (currentTerm === "") {
				// don't need to show anything
				$searchDialog.attr("data-results-visible", "0");
			}
			else if ($results.length > 0) {
				$self.attr("data-results-visible", "1");
				for (var i=0; i<$results.length; i++) {
					var $result = $results[i]; 
					$resultsContainer.append($result);
				}
			}
			else {
				$searchDialog.attr("data-results-visible", "1");
				var $msg = $("<div />").addClass("no-results-msg").text("No results found.")
				$resultsContainer.append($msg);
			}
		}
	});
});