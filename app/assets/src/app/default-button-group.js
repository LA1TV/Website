// will attach a ButtonGroup to elements with .default-button-group

define(["jquery", "./components/button-group"], function($, ButtonGroup) {
	var registerDefaultButtonGroup = register;

	$(document).ready(function() {
		$(".default-button-group").each(function() {
			register($(this).first());
		});
	});
	
	function register($container) {
		var destinationName = $container.attr("data-destinationname");
		// the reference to the hidden form element where chosen id should be placed
		var $destinationEl = $container.parent().find('[name="'+destinationName+'"]').first();
		var buttonsData = $.parseJSON($container.attr("data-buttonsdata"));
		var optionRequired = $container.attr("data-optionrequired") === "1";
		var chosenId = $destinationEl.val() !== "" ? parseInt($destinationEl.val()) : null
	
		var buttonGroup = new ButtonGroup(buttonsData, optionRequired, {
			id: chosenId
		});
		$(buttonGroup).on("stateChanged", function() {
			$destinationEl.val(buttonGroup.getId() !== null ? buttonGroup.getId() : "");
		});
		$container.append(buttonGroup.getEl());
		
		return buttonGroup;
	}

	return {
		register: registerDefaultButtonGroup
	};
});