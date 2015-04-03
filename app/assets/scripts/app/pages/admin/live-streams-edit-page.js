define([
	"jquery",
	"../../page-data",
	"../../components/reorderable-list",
	"../../components/quality-and-url-input",
	"lib/domReady!"
], function($, PageData, ReorderableList, QualityAndUrlInput) {
	
	$(".page-livestreams-edit").first().each(function() {
	
		$pageContainer = $(this).first();
		
		$pageContainer.find(".form-urls").each(function() {
			var $container = $(this).first();
			var $destinationEl = $container.parent().find('[name="urls"]').first();
			var initialDataStr = $(this).attr("data-initialdata");
			var initialData = jQuery.parseJSON(initialDataStr);
			
			var reorderableList = new ReorderableList(true, true, true, function(state) {
				var qualityAndUrlInput = new QualityAndUrlInput(state);
				return qualityAndUrlInput;
			}, {
				url: "",
				type: "",
				support: "all",
				qualityState: {id: null, text: null}
			}, initialData);
			$(reorderableList).on("stateChanged", function() {
				$destinationEl.val(JSON.stringify(reorderableList.getState()));
			});
			$container.append(reorderableList.getEl());
		});
	
	});
});