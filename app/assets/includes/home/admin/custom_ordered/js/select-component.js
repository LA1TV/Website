var SelectComponent = null;

$(document).ready(function() {

	SelectComponent = function(forPassword, state) {
		
		var self = this;
		
		this.getId = function() {
			return null;
		};
		
		this.getState = function() {
			return {
				value: $el.val()
			};
		};
		
		this.setState = function(state) {
			$el.val(state.value);
			currentValue = state.value;
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $el;
		};
		
		var currentValue = null;
		var $el = $("<input />").addClass("form-control");
		$el.prop("type", forPassword ? "password" : "text");
		
		$el.on("keyup change", function() {
			var value = $el.val();
			if (currentValue !== value) {
				currentValue = value;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		this.setState(state);
	};
});