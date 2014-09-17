$(document).ready(function() {

	$(".page-playlists-edit").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".form-show").each(function() {
			
			function render() {
				if (ajaxSelect.getId() !== null) {
					$seriesNoContainer.show();
				}
				else {
					$seriesNoContainer.hide();
					$seriesNoInput.val("");
				}
			}
		
			var ajaxSelect = registerDefaultAjaxSelect($(this).first());
			var $seriesNoContainer = $pageContainer.find(".series-no-container").first();
			var $seriesNoInput = $seriesNoContainer.find("input").first();
			$(ajaxSelect).on("stateChanged", function() {
				render();
			});
			render();
		});
		
		$pageContainer.find(".form-playlist-content").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="playlist-content"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reorderableList = new ReorderableList(true, true, true, function(state) {
				var ajaxSelect = new AjaxSelect(baseUrl+"/admin/media/ajaxselect", state);
				$(ajaxSelect).on("dropdownOpened", function() {
					reorderableList.scrollToComponent(ajaxSelect);
				});
				return ajaxSelect;
			}, {
				id: null,
				text: null
			}, initialData);
			$(reorderableList).on("stateChanged", function() {
				$destinationEl.val(JSON.stringify(reorderableList.getIds()));
			});
			$container.append(reorderableList.getEl());
		});
		
		$pageContainer.find(".form-related-items").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="related-items"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reorderableList = new ReorderableList(true, true, true, function(state) {
				var ajaxSelect = new AjaxSelect(baseUrl+"/admin/media/ajaxselect", state);
				$(ajaxSelect).on("dropdownOpened", function() {
					reorderableList.scrollToComponent(ajaxSelect);
				});
				return ajaxSelect;
			}, {
				id: null,
				text: null
			}, initialData);
			$(reorderableList).on("stateChanged", function() {
				$destinationEl.val(JSON.stringify(reorderableList.getIds()));
			});
			$container.append(reorderableList.getEl());
		});
	});
});