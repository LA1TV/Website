define([
	"jquery",
	"../../components/button-group",
	"../../page-data",
	"lib/domReady!"
], function($, ButtonGroup, PageData) {
	
	$(".page-account").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".facebook-notifications-button-group-container").each(function() {

			var self = this;
			
			var buttonsData = jQuery.parseJSON($(this).attr("data-buttonsdata"));
			var chosenId = parseInt($(this).attr("data-chosenid"));
			var currentId = chosenId;
			var buttonGroup = new ButtonGroup(buttonsData, true, {
				id: chosenId
			});
			$(buttonGroup).on("stateChanged", function() {
				makeAjaxRequest(buttonGroup.getId());
			});
			
			$(this).append(buttonGroup.getEl());
			
			function makeAjaxRequest(id) {
				if (id === currentId) {
					// only make request if id has changed.
					return;
				}
				
				jQuery.ajax(PageData.get("baseUrl")+"/account/set-facebook-notifications-state", {
					cache: false,
					dataType: "json",
					data: {
						csrf_token: PageData.get("csrfToken"),
						state_id: id
					},
					type: "POST"
				}).always(function(data, textStatus, jqXHR) {
					if (jqXHR.status === 200) {
						currentId = data.state_id;
					}
					else {
						buttonGroup.setState({id: currentId});
					}
				});
			}
		});
		
	});
	
});