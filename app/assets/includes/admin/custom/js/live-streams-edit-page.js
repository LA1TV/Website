$(document).ready(function() {
	
	$(".page-live-streams-edit").first().each(function() {
		
		$pageContainer.find(".form-qualities").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="form-qualities"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reordableList = new ReordableList(true, true, true, function(state) {
				return new AjaxSelect(baseUrl+"/admin/qualities/ajaxselect", state);
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