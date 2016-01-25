define(["jquery"], function($) {

	// buttonsData should be an array of {id, text} for the buttons
	var ButtonGroup = function(buttonsData, optionRequired, state) {
		
		var self = this;
		
		this.getId = function() {
			return chosenId;
		};
		
		this.getState = function() {
			return {
				id: chosenId
			};
		};
		
		this.setState = function(state) {
			previousId = chosenId;
			chosenId = state.id;
			render();
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $container;
		};
		
		var chosenId = null;
		var previousId = null;
		var selectedClass = "btn-primary";
		var unSelectedClass = "btn-default";
		
		// array of {id, $el}
		var buttons = [];
		var $container = $("<div />").addClass("button-group-component");
		var $btnGroup = $("<div />").addClass("btn-group");
		
		for (var i=0; i<buttonsData.length; i++) {
			var data = buttonsData[i];
			var button = {
				id: data.id,
				$el: $("<button />").attr("data-id", data.id).prop("type", "button").addClass("btn").addClass(unSelectedClass).text(data.text)
			};
			buttons.push(button);
			(function(button) {
				button.$el.click(function() {
					var state = null;
					if (!optionRequired && button.id === chosenId) {
						state = {
							id: null
						};
					}
					else {
						state = {
							id: button.id
						};
					}
					self.setState(state);
				});
			})(button);
			$btnGroup.append(button.$el);
		}
		$container.append($btnGroup);
		this.setState(state);
		
		function render() {
			if (chosenId !== null && previousId === chosenId) {
				return;
			}
			if (previousId !== null) {
				var $btn = $btnGroup.find('[data-id="'+previousId+'"]').first();
				$btn.removeClass(selectedClass);
				$btn.addClass(unSelectedClass);
				$btn.blur();
			}
			if (chosenId !== null) {
				var $btn = $btnGroup.find('[data-id="'+chosenId+'"]').first();
				$btn.removeClass(unSelectedClass);
				$btn.addClass(selectedClass);
			}
		}
	}
	return ButtonGroup;
});