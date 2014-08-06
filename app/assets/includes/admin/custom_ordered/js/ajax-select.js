var AjaxSelect = null;

$(document).ready(function() {

	AjaxSelect = function(dataSourceUri, state) {
		
		var self = this;
		
		this.getId = function() {
			return chosenItemId;
		};
		
		this.getState = function() {
			return {
				id: chosenItemId,
				text: chosenItemText
			};
		};
		
		this.setState = function(state) {
			var item = null;
			if (state.id !== null) {
				item = {
					id: state.id,
					text: state.text
				};
			}	
			setItem(item); // calls render
		};
		
		this.getEl = function() {
			return $container;
		};
		
		var $container = $("<div />").addClass("ajax-select");
		
		var chosenItemId = null;
		var chosenItemText = null;
		var currentChosenItemText = null;
		var hasResult = null;
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
		
		var $hasResult = $('<div />').addClass("has-result");
		var $resultTable = $('<div />').addClass("result-table");
		var $resultRow = $('<div />').addClass("result-row");
		var $resultContainer = $('<div />').addClass("result-container");
		var $buttonContainer = $('<div />').addClass("button-container");
		var $clearButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-default").html("&times;");
		var $searching = $('<div />').addClass("searching");
		var $search = $('<input />').prop("type", "text").prop("placeholder", "Search...").addClass("form-control search");
		var $loading = $('<div />').addClass("loading").html('<img src="'+assetsBaseUrl+'assets/admin/img/loading.gif"> <span class="txt">Loading...</span>');
		var $results = $('<div />').addClass("results").hide();
		var $noResults = $('<div />').addClass("no-results").html('No results.').hide();
		
		$clearButton.click(function() {
			setId(null);
		});
		
		$hasResult.append($resultTable);
		$resultTable.append($resultRow);
		$resultRow.append($resultContainer);
		$resultRow.append($buttonContainer);
		$buttonContainer.append($clearButton);
		$searching.append($search);
		$searching.append($loading);
		$searching.append($noResults);
		$searching.append($results);

		this.setState(state); // this calls render()
		
		$container.append($hasResult);
		$container.append($searching);
		
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
				if (loading || hasResult || results.length === 0 || defaultResult === null) {
					return;
				}
				setId(defaultResult);
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
			var localHasResult = chosenItemId !== null;
			if (hasResult || hasResult !== localHasResult) {
				hasResult = localHasResult;
				if (hasResult) {
					if (currentChosenItemText === chosenItemText) {
						return;
					}
					currentChosenItemText = chosenItemText;
					loading = false;
					renderLoading();
					$searching.hide();
					$resultContainer.text(chosenItemText);
					$hasResult.show();
				}
				else {
					currentChosenItemText = null;
					loading = true;
					renderLoading();
					term = null;
					$search.val("");
					updateResults();
					renderHighlighted();
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
						var id = result.id;
						$el.hover(function() {
							hoveredResult = id;
							renderHighlighted();
						}, function() {
							if (hoveredResult === id) {
								hoveredResult = null;
							}
							renderHighlighted();
						});
						
						$el.click(function() {
							setId(id);
						});
					})();
					
					$resultRows.push($el);
					$results.append($el);
				}
				defaultResult = results.length > 0 ? results[0].id : null;
				currentlyHighlightedResult = null;
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
				if (results.length === 0) {
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
			
			jQuery.ajax(dataSourceUri, {
				cache: false,
				dataType: "json",
				data: {
					term: term,
					csrf_token: getCsrfToken()
				},
				type: "POST"
			}).done(function(data) {
				if (data.success) {
					var payload = data.payload;
					resultsChanged = true;
					results = payload.results;
					if (loading && term === payload.term) {
						// makes sure that this response is for the latest term that was requested
						renderResults();
					}
				}
			});
		}
		
		function getTermChanged() {
			return $search.val() !== term;
		}
		
		function setId(id) {
			if (id === null) {
				setItem(null);
			}
			else {
				var index = resultsIds.indexOf(id);
				setItem(index !== -1 ? results[index] : null);
			}
		};
		
		function setItem(item) {
			if (item !== null) {
				chosenItemId = item.id;
				chosenItemText = item.text;
			}
			else {
				chosenItemId = null;
				chosenItemText = null;
			}
			$(self).triggerHandler("stateChanged");
			render();
		}
	};
});