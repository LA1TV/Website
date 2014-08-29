$(document).ready(function() {
	
	$(".page-livestreams-edit").first().each(function() {
	
		$pageContainer = $(this).first();
		
		$pageContainer.find(".form-qualities").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="qualities"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reorderableList = new ReorderableList(true, true, true, function(state) {
				var ajaxSelect = new AjaxSelect(baseUrl+"/admin/live-stream-qualities/ajaxselect", state);
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