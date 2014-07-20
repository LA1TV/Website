// handles all .ajax-select

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	
	$(".ajax-select").each(function() {
		var dataSourceUri = $(this).attr("data-datasourceuri");
		var destinationName = $(this).attr("data-destinationname");
		var currentItemName = $(this).attr("data-currentitemname");
		// the reference to the hidden form element where chosen rows id should be placed
		var $destinationEl = $(this).parent().find('[name="'+destinationName+'"]').first();
		
		var hasResult = null;
		var id = null;
		var changeTimerId = null;
		
		var $hasResult = $('<div />').addClass("has-result");
		var $resultContainer = $('<div />').addClass("result-container");
		var $buttonContainer = $('<div />').addClass("button-container");
		var $clearButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-default").html("&times;");
		var $searching = $('<div />').addClass("searching");
		var $search = $('<input />').prop("type", "text").prop("placeholder", "Search...").addClass("search");
		var $results = $('<div />').addClass("results");
		
		$hasResult.append($resultContainer);
		$hasResult.append($buttonContainer);
		$buttonContainer.append($clearButton);
		$searching.append($search);
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
		
		$search.keypress(function(e) {
		
			if (changeTimerId !== null) {
				clearTimeout(changeTimerId);
			}
			if (e.which === 13) { // enter
				updateResults();
			}
			else {
				changeTimerId = setTimeout(function() {
					updateResults();
				}, 500);
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
					$hasResult.hide();
					$searching.show();
				}
			}
		}
		
		// update the results using the current search term
		function updateResults() {
			var term = $search.val();
			if (term === "") {
				term = null;
			}
			
			var response = [
				{id: 1, text: "Item 1"},
				{id: 2, text: "Item 2"}
			];
		
			$results.hide();
			$results.html("");
		
			for (var i=0; i<response.length; i++) {
				var result = response[i];
				var $el = $('<div />').addClass("result-row").attr("data-id", result.id).attr("data-selected", i===0?"1":"0").text(result.text);
				$results.append($el);
			}
			$results.show();
			
		}
	});
	
});