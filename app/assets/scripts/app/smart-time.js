// handles updating dom elements containing a reference to a time

define(["jquery", "./synchronised-time"], function($, SynchronisedTime) {

	var SmartTime = function($el, time) {
		var currentText = null;
		update();
		var timerId = setInterval(update, 1000);
		
		function update() {
			var text = $.format.prettyDate(time, function() {
				return SynchronisedTime.getDate();
			});
			if (currentText !== text) {
				$el.text(text);
				currentText = text;
			}
		}
		
		return {
			destroy: function() {
				clearTimeout(timerId);
			}
		};
	};
	
	return SmartTime;
});