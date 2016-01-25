define([
	"jquery",
	"../../page-data",
	"../../components/reorderable-list",
	"../../components/quality-and-url-input"
], function($, PageData, ReorderableList, QualityAndUrlInput) {
	
	$(document).ready(function() {
		$(".page-livestreams-edit").first().each(function() {
		
			var $pageContainer = $(this).first();
			
			$pageContainer.find(".form-urls").each(function() {
				var $container = $(this).first();
				var $destinationEl = $container.parent().find('[name="urls"]').first();
				var initialDataStr = $(this).attr("data-initialdata");
				var initialData = $.parseJSON(initialDataStr);
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					var qualityAndUrlInput = new QualityAndUrlInput(state);
					return qualityAndUrlInput;
				}, {
					url: "",
					dvrBridgeServiceUrl: false,
					nativeDvr: false,
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
});