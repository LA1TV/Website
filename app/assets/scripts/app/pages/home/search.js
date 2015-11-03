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
		var pendingQueryDelay = 250;
		var currentTerm = "";
		var currentSearchInputVal = "";
		var termBeingQueried = null;

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
			currentSearchInputVal = "";
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

				(function() {

					$searchInput.keyup(function(e) {
						if (e.keyCode === 13) {
							// enter key
							runIfChanged(function(txt) {
								submitQuery(txt);
							});
						}
						else {
							runIfChanged(function(txt) {
								prepareQuery(txt);
							});
						}
					});
					$searchInput.change(function() {
						runIfChanged(function(txt) {
							submitQuery(txt);
						});
					});

					// calls the callback with the text if it's changed
					function runIfChanged(callback) {
						var val = $searchInput.val();
						if (val !== currentSearchInputVal) {
							currentSearchInputVal = val;
							callback(val);
						}
					}
				})()

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
					targetAttachment: 'bottom right',
					constraints: [
						{
							to: $(document)[0],
							attachment: 'element'
						}
					]
				});
			}
			return $searchDialog;
		}

		function buildResult(result) {
			var $result = $("<div />").addClass("search-result");
			var $thumb = $("<div />").addClass("theThumbnail").css("background-image", 'url("'+result.thumbnailUri+'")');
			var $info = $("<div />").addClass("info");
			var $title = $("<div />").addClass("title").text(result.title);
			var $description = null;
			if (result.description) {
				$description = $("<div />").addClass("description").text(result.description);
			}
			$result.append($thumb);
			$info.append($title);
			if ($description) {
				$info.append($description);
			}
			$result.append($info);
			$result.click(function() {
				window.location = result.url;
			});
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
			renderResults();
		}

		function cancelPendingQuery() {
			if (pendingQueryTimeoutId !== null) {
				clearTimeout(pendingQueryTimeoutId);
				pendingQueryTimeoutId = null;
				renderResults();
			}
		}

		function submitQuery(term) {

			if (termBeingQueried !== null) {
				// there is already a query in progress
				if (termBeingQueried === term) {
					// let the current query continue
					return;
				}
			}
			else {
				if (currentTerm === term) {
					// the current results are already for this term
					renderResults();
					return;
				}
			}

			// abort a previous query if there is one
			cancelQuery();
			cancelPendingQuery();

			termBeingQueried = term;

			if (term === "") {
				// pretend there are now results if not entered term
				onResults([]);
				return;
			}

			renderResults();

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
				termBeingQueried = null;
				currentTerm = term;
				for(var i=0; i<results.length; i++) {
					var result = results[i];
					var $result = buildResult(result);
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
				}
				renderResults();
			}
		}

		function cancelQuery() {
			if (queryXhr) {
				// abort a previous query if there is one
				queryXhr.abort();
				termBeingQueried = null;
			}
		}

		function renderResults() {
			$resultsContainer.empty();

			resultIndexUnderCursor = null;
			resultIndexWithArrowKeys = null;

			var resultsAreaVisible = true;
			if (termBeingQueried !== null || pendingQueryTimeoutId !== null) {
				// there is a query in progress, or about to happen
				var $msg = $("<div />").addClass("msg loading-msg");
				$msg.append($("<img />").attr("src", PageData.get("assetsBaseUrl")+'assets/admin/img/loading.gif'));
				$msg.append($("<span />").text(" Loading..."));
				$resultsContainer.append($msg);
			}
			else if (currentTerm === "") {
				resultsAreaVisible = false;
			}
			else if ($results.length > 0) {
				for (var i=0; i<$results.length; i++) {
					var $result = $results[i]; 
					$resultsContainer.append($result);
				}
			}
			else {
				var $msg = $("<div />").addClass("msg no-results-msg").text("No results found.")
				$resultsContainer.append($msg);
			}
			$searchDialog.attr("data-results-visible", resultsAreaVisible ? "1":"0");
		}
	});
});