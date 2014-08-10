$(document).ready(function() {

	$(".page-playlists-edit").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".form-series").each(function() {
			
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
			
			var reordableList = new ReordableList(true, true, true, function(state) {
				return new AjaxSelect(baseUrl+"/admin/media/ajaxselect", state);
			}, {
				id: null,
				text: null
			}, initialData);
			$(reordableList).on("stateChanged", function() {
				$destinationEl.val(JSON.stringify(reordableList.getIds()));
			});
			$container.append(reordableList.getEl());
		});
	});
});