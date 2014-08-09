$(document).ready(function() {

	$(".page-playlists-edit").first().each(function() {
	
		var $pageContainer = $(this).first();
	
		$pageContainer.find(".form-playlist-content").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="playlist-content"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reordableList = new ReordableList(true, true, function(state) {
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