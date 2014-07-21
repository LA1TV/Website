// handles all .ajax-select

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	$(".ajax-select").each(function() {
		var dataSourceUri = $(this).attr("data-datasourceuri");
		var destinationName = $(this).attr("data-destinationname");
		var currentItemName = $(this).attr("data-currentitemname");
		// the reference to the hidden form element where chosen rows id should be placed
		var $destinationEl = $(this).parent().find('[name="'+destinationName+'"]').first();
		
		var hasResult = null;
		var id = null;
		var changeTimerId = null;
		var results = [];
		var resultsIds = [];
		var $resultRows = [];
		// the index of the result that should be chosen with the enter key
		var defaultResult = null;
		// the index of the result that is currently under the mouse 
		var hoveredResult = null;
		var currentlyHighlightedResult = null;
		var term = null;
		var loading = true;
		var loadingVisible = true;
		var resultsChanged = false;
		var hasNoResults = false;
		
		var $hasResult = $('<div />').addClass("has-result");
		var $resultContainer = $('<div />').addClass("result-container");
		var $buttonContainer = $('<div />').addClass("button-container");
		var $clearButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-default").html("&times;");
		var $searching = $('<div />').addClass("searching");
		var $search = $('<input />').prop("type", "text").prop("placeholder", "Search...").addClass("form-control search");
		var $loading = $('<div />').addClass("loading").html('<img src="'+assetsBaseUrl+'assets/admin/img/loading.gif"> <span class="txt">Loading...</span>');
		var $results = $('<div />').addClass("results").hide();
		var $noResults = $('<div />').addClass("no-results").html('No results.').hide();
		
		$hasResult.append($resultContainer);
		$hasResult.append($buttonContainer);
		$buttonContainer.append($clearButton);
		$searching.append($search);
		$searching.append($loading);
		$searching.append($noResults);
		$searching.append($results);
		
		if ($destinationEl.val() !== "") {
			id = parseInt($destinationEl.val());
		}
		
		render();
		
		$(this).append($hasResult);
		$(this).append($searching);
		
		if (!hasResult) {
			updateResults();
		}
		
		$search.keyup(function(e) {
			
			if (e.which === 13) { // enter
				// ignore enter here because this is handled in keydown
				return;
			}
			
			if (changeTimerId !== null) {
				clearTimeout(changeTimerId);
			}
			
			if (!getTermChanged()) {
				// term hasn't changed so do nothing
				loading = false;
				renderLoading();
				return;
			}
			
			changeTimerId = setTimeout(function() {
				updateResults();
			}, 500);
			loading = true;
			renderLoading();
		});
		
		$searching.keydown(function(e) {
			
			if (e.which === 13) { // enter
				e.preventDefault();
				
				if (changeTimerId !== null) {
					clearTimeout(changeTimerId);
				}
				
				if (!getTermChanged()) {
					// term hasn't changed so do nothing
					loading = false;
					renderLoading();
					return;
				}
				
				loading = true;
				renderLoading();
				updateResults();
				return;
			}
			
			if (!loading && !hasResult && defaultResult !== null) {
				if (e.which === 38) { //up
					e.preventDefault();
					var i = resultsIds.indexOf(defaultResult);
					if (i > 0) {
						defaultResult = resultsIds[i-1];
						renderHighlighted();
					}
					return;
				}
				else if (e.which === 40) { // down
					e.preventDefault();
					var i = resultsIds.indexOf(defaultResult);
					if (i < resultsIds.length-1) {
						defaultResult = resultsIds[i+1];
						renderHighlighted();
					}
					return;
				}
			}
		});
		
		function render() {
			var localHasResult = id !== null;
			if (hasResult !== localHasResult) {
				hasResult = localHasResult;
				if (hasResult) {
					$searching.hide();
					$hasResult.show();
				}
				else {
					renderLoading();
					renderHighlighted();
					renderResults();
					$hasResult.hide();
					$searching.show();
				}
			}
		}
		
		function renderHighlighted() {
			if (currentlyHighlightedResult !== null) {
				$resultRows[resultsIds.indexOf(currentlyHighlightedResult)].attr("data-selected", 0);
			}
			if (hoveredResult !== null) {
				$resultRows[resultsIds.indexOf(hoveredResult)].attr("data-selected", 1);
				currentlyHighlightedResult = hoveredResult;
			}
			else if (defaultResult !== null) {
				$resultRows[resultsIds.indexOf(defaultResult)].attr("data-selected", 1);
				currentlyHighlightedResult = defaultResult;
			}
		}
		
		function renderResults() {
			if (resultsChanged) {
				resultsChanged = false;
				$results.html("");
				resultsIds = [];
				$resultRows = [];
				for (var i=0; i<results.length; i++) {
					var result = results[i];
					resultsIds.push(result.id);
					var $el = $('<div />').addClass("result-row").attr("data-id", result.id).attr("data-selected", "0").text(result.text);
					
					(function() {
						var elId = result.id;
						$el.hover(function() {
							hoveredResult = elId;
							renderHighlighted();
						}, function() {
							if (hoveredResult === elId) {
								hoveredResult = null;
							}
							renderHighlighted();
						});
					})();
					
					$resultRows.push($el);
					$results.append($el);
				}
				defaultResult = results.length > 0 ? results[0].id : null;
				highlightedResult = null;
				renderHighlighted();
				loading = false;
				renderLoading();
			}
		}
		
		function renderLoading() {
			if (loadingVisible === loading) {
				return;
			}
			if (loading) {
				$noResults.hide();
				$results.hide();
				$loading.show();
			}
			else {
				$loading.hide();
				if (hasNoResults) {
					$noResults.show();
				}
				else {
					$results.show();
				}
			}
			loadingVisible = loading;
		}
		
		// update the results using the current search term
		function updateResults() {
			if (!getTermChanged()) {
				return;
			}
			term = $search.val();
			resultsChanged = true;
			results = [
				{id: 1, text: "Item 1"},
				{id: 2, text: "Item 2"}
			];
			// callback after ajax request
			setTimeout(function() {
				renderResults();
			}, 500);
		}
		
		function getTermChanged() {
			return $search.val() !== term;
		}
	});
	
});