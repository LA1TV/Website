var $ = require("jquery");
require("./toggleable.css");

/*
*  componentBuilder should be a function that returns a class with the following functions:
*  - getEl() return the dom element of the element.
*  - getId() return an id representing the chosen option
*  - setState(state) set the the chosen option using a state object
*  - getState() return the state object for the element
*  - destroy() [optional] called when the component is being destroyed.
*
*  - It will get passed the initial state object as the first parameter
*/
var ToggleableComponent = function(enableToggle, componentBuilder, initialComponentState, state) {
	
	var self = this;
	
	this.getId = function() {
		return component !== null  ? component.getId() : null;
	};
	
	this.getState = function() {
		return {
			componentToggled: component !== null,
			componentState: component !== null ? component.getState() : null
		};
	};
	
	this.setState = function(state) {
		if (state.componentToggled) {
			if (component === null) {
				component = componentBuilder(state.componentState);
				$(component).on("stateChanged", function() {
					$(self).triggerHandler("stateChanged");
				});
				$componentCol.append(component.getEl());
				$componentUnToggled.hide();
				$componentToggled.show();
				$(self).triggerHandler("stateChanged");
			}
			else {
				component.setState(state.componentState);
			}
		}
		else {
			if (component !== null) {
				$componentToggled.hide();
				$componentUnToggled.show();
				component.getEl().remove();
				if (typeof(component.destroy) === "function") {
					component.destroy();
				}
				component = null;
				$(self).triggerHandler("stateChanged");
			}
		}
	};
	
	this.getEl = function() {
		return $container;
	};
	
	this.destroy = function() {
		if (component !== null) {
			if (typeof(component.destroy) === "function") {
				component.destroy();
			}
		}
	};
	
	var component = null; // stores the component when it has been toggled on
	
	var $container = $("<div />").addClass("toggleable-component");
	var $componentToggled = $("<div />").addClass("component-toggled");
	var $table = $("<div />").addClass("table-container");
	var $row = $("<div />").addClass("row-container");
	var $componentCol = $("<div />").addClass("component-col");
	var $buttonsCol = $("<div />").addClass("buttons-col");
	var $buttonsContainer = $("<div />").addClass("buttons-container");
	var $unToggleButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-default").html("&times;");
	var $componentUnToggled = $("<div />").addClass("component-untoggled");
	var $toggleButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-block btn-info").html("Click To Change");
	
	$toggleButton.click(function() {
		self.setState({
			componentToggled: true,
			componentState: initialComponentState
		});
	});
	
	$unToggleButton.click(function() {
		self.setState({
			componentToggled: false,
			componentState: null
		});
	});
	
	$componentToggled.append($table);
	$table.append($row);
	$row.append($componentCol);
	$row.append($buttonsCol);
	$buttonsCol.append($buttonsContainer);
	$buttonsContainer.append($unToggleButton);
	$unToggleButton.prop("disabled", !enableToggle);
	$componentUnToggled.append($toggleButton);
	$componentToggled.hide();
	
	$container.append($componentToggled);
	$container.append($componentUnToggled);
	
	this.setState(state);
};

module.exports = ToggleableComponent;