define(["jquery"], function($) {

	var AutoContinueButton = function(state) {
		
		var self = this;
		
		this.getId = function() {
			return null;
		};
		
		this.getState = function() {
			return {
				mode: mode
			};
		};
		
		this.setState = function(state) {
			mode = state.mode;
			render();
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $container;
		};
		
		this.getMode = function() {
			return mode;
		};
		
		var mode = state.mode; // 0 = disabled, 1 = auto continue, 2 = auto continue and loop
		
		var $container = $("<div />").addClass("auto-continue-button");
		var $autoContinueBtn = $("<button />").attr("type", "button").addClass("btn btn-xs");
		$autoContinueBtn.append($("<span />").addClass("glyphicon glyphicon-repeat"));
		$container.append($autoContinueBtn);
		render();
		
		$autoContinueBtn.click(function() {
			if (shifted && mode !== 2) {
				// shift key being held down
				mode = 2;
			}
			else if (mode === 0) {
				mode = 1;
			}
			else {
				mode = 0;
			}
			if (mode !== state.mode) {
				render();
				state.mode = mode;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		function render() {
			$autoContinueBtn.removeClass("btn-default btn-info btn-danger active");
			if (mode === 0) {
				$autoContinueBtn.addClass("btn-default");
			}
			else if (mode === 1) {
				$autoContinueBtn.addClass("active btn-info");
			}
			else if (mode === 2) {
				$autoContinueBtn.addClass("active btn-danger");
			}
			else {
				throw "Unknown auto continue mode.";
			}
			$autoContinueBtn.attr("aria-pressed", mode !== 0);
		}
		
		var shifted = false;
		$(document).on('keyup keydown', function(e){
			shifted = e.shiftKey;
			return true;
		});
	}
	return AutoContinueButton;
});