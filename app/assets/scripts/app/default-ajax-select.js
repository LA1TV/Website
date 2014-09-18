// will attach an AjaxSelect to elements with .default-ajax-select

define([
	"jquery",
	"./components/ajax-select",
	"lib/domReady!"
], function($, AjaxSelect) {

	var registerDefaultAjaxSelect = register;

	$(".default-ajax-select").each(function() {
		register($(this).first());
	});
	
	function register($container) {
		var destinationName = $container.attr("data-destinationname");
		// the reference to the hidden form element where chosen rows id should be placed
		var $destinationEl = $container.parent().find('[name="'+destinationName+'"]').first();
		var dataSourceUri = $container.attr("data-datasourceuri");
		var chosenItemId = $destinationEl.val() !== "" ? parseInt($destinationEl.val()) : null
		var chosenItemText = $container.attr("data-chosenitemtext");
	
		var ajaxSelect = new AjaxSelect(dataSourceUri, {
			id: chosenItemId,
			text: chosenItemText
		});
		$(ajaxSelect).on("stateChanged", function() {
			$destinationEl.val(ajaxSelect.getId() !== null ? ajaxSelect.getId() : "");
		});
		$container.append(ajaxSelect.getEl());
		
		return ajaxSelect;
	};
	
	return {
		register: registerDefaultAjaxSelect
	};
});