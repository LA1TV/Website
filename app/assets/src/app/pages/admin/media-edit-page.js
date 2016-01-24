define([
	"jquery",
	"../../page-data",
	"../../helpers/ajax-helpers",
	"../../pinger",
	"../../custom-form",
	"../../components/reorderable-list",
	"../../components/ajax-select",
	"../../components/ajax-upload",
	"../../components/chapter-input",
	"../../components/credit-input"
], function($, PageData, AjaxHelpers, Pinger, CustomForm, ReorderableList, AjaxSelect, AjaxUpload, ChapterInput, CreditInput) {
	
	$(document).ready(function() {
		$(".page-media-edit").first().each(function() {
		
			new Pinger(); // keeps the session alive by making a request at regular intervals
			
			CustomForm.addHandler(1, function() {
				if (AjaxUpload.getNoActiveUploads() > 0) {
					alert("There are still uploads are in progress.\n\nPlease either cancel them or wait for them to finish before saving.");
					return false; // cancels submit
				}
				return true;
			});
			
			(function() {
				var disableFn = function($panel, $enabledInput) {
					var $disabledContainer = $panel.find(".disabled-container");
					var $enabledContainer = $panel.find(".enabled-container");
					$enabledContainer.hide();
					$enabledContainer.find('[data-virtualform]').attr("disabled", true);
					$enabledInput.val("0");
					$disabledContainer.show();
				};
				
				var enableFn = function($panel, $enabledInput) {
					var $disabledContainer = $panel.find(".disabled-container");
					var $enabledContainer = $panel.find(".enabled-container");
					$disabledContainer.hide();
					$enabledContainer.find('[data-virtualform]').prop("disabled", false);
					$enabledInput.val("1");
					$enabledContainer.show();
				}
				
				var handler = function() {
					var $panel = $(this).first();
					var $enabledInput = $panel.find('.enabled-input input').first();
					$enabledInput.val() === "1" ? enableFn($panel, $enabledInput) : disableFn($panel, $enabledInput);
					
					$(this).find(".disabled-container .enable-button").click(function(){enableFn($panel, $enabledInput);});
					$(this).find(".enabled-container .disable-button").click(function() {
						if (!confirm("Are you sure you want to remove this?")) {
							return;
						}
						disableFn($panel, $enabledInput);
					});
				};
				
				$(".page-media-edit .vod-panel").each(handler);
				$(".page-media-edit .live-stream-panel").each(handler);
			})();
			
			$(this).find(".form-credits").each(function() {
				var $container = $(this).first();
				var $destinationEl = $container.parent().find('[name="credits"]').first();
				var initialDataStr = $(this).attr("data-initialdata");
				var initialData = $.parseJSON(initialDataStr);
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					return new CreditInput(state);
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
			
			$(this).find(".form-related-items").each(function() {
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
			
			$(this).find(".form-vod-chapters").each(function() {
				var $container = $(this).first();
				var $destinationEl = $container.parent().find('[name="vod-chapters"]').first();
				var initialDataStr = $(this).attr("data-initialdata");
				var initialData = $.parseJSON(initialDataStr);
				
				
				var reorderableList = new ReorderableList(true, true, true, function(state) {
					return new ChapterInput(state);
				}, {
					title: "",
					time: null
				}, initialData);
				$(reorderableList).on("stateChanged", function() {
					$destinationEl.val(JSON.stringify(reorderableList.getState()));
				});
				$container.append(reorderableList.getEl());
			});
			
			$(this).find(".remove-dvr-recording-btn-container").each(function() {
				var self = this;
				
				var removeUri = $(this).attr("data-ajax-remove-uri");
				var $btn = $(this).find(".remove-btn").first();
				
				$btn.click(function() {
					if (!confirm("Are you sure you want to remove the dvr recording?\n\nTHIS OPERATION CANNOT BE UNDONE.")) {
						return;
					}
					$btn.prop("disabled", true);
					$.ajax(removeUri, {
						cache: false,
						dataType: "json",
						headers: AjaxHelpers.getHeaders(),
						data: {
							csrf_token: PageData.get("csrfToken")
						},
						type: "POST"
					}).always(function(data, textStatus, jqXHR) {
						if (jqXHR.status === 200 && data.success) {
							$(self).remove();
							alert("The dvr recording has been deleted.");
						}
						else {
							$btn.prop("disabled", false);
							alert("The dvr recording could not be removed for some reason.");
						}
					});
				});
			});
		});
	});
});