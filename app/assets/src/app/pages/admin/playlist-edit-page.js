define([
	"jquery",
	"../../page-data",
	"../../default-ajax-select",
	"../../components/reorderable-list",
	"../../components/ajax-select",
	"../../components/credit-input"
], function($, PageData, DefaultAjaxSelect, ReorderableList, AjaxSelect, CreditInput) {

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
			
				var ajaxSelect = DefaultAjaxSelect.register($(this).first());
				var $seriesNoContainer = $pageContainer.find(".series-no-container").first();
				var $seriesNoInput = $seriesNoContainer.find("input").first();
				$(ajaxSelect).on("stateChanged", function() {
					render();
				});
				render();
			});
			
			$(this).find(".form-credits").each(function() {
				var $container = $(this).first();
				var $destinationEl = $container.parent().find('[name="credits"]').first();
				var initialDataStr = $(this).attr("data-initialdata");
				var initialData = $.parseJSON(initialDataStr);
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					return new CreditInput("playlist", state);
				}, {
					productionRoleState: {id: null, text: null},
					siteUserState: {id: null, text: null},
					nameOverride: null
				}, initialData);
				$(reorderableList).on("stateChanged", function() {
					var output = [];
					var state = reorderableList.getState();
					for (var i=0; i<state.length; i++) {
						var row = state[i];
						output.push({
							productionRoleId: row.productionRoleState.id,
							siteUserId: row.nameOverride !== null ? null : row.siteUserState.id,
							nameOverride: row.nameOverride
						});
					}
					$destinationEl.val(JSON.stringify(output));
				});
				$container.append(reorderableList.getEl());
			});

			$pageContainer.find(".form-playlist-content").each(function() {
				var $container = $(this).first();
				var $destinationEl = $container.parent().find('[name="playlist-content"]').first();
				var initialDataStr = $(this).attr("data-initialdata");
				var initialData = $.parseJSON(initialDataStr);
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					var ajaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/media/ajaxselect", state);
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
				var initialData = $.parseJSON(initialDataStr);
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					var ajaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/media/ajaxselect", state);
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
});