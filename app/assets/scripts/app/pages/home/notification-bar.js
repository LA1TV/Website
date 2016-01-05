define([
	"jquery",
	"lib/domReady!"
], function($) {
	// array of {priority, $el} ordered by priority ascending
	var notifications = [];

	var $notificationBar = $("<div />").addClass("notification-bar");
	var $filler = $("<div />").addClass("notification-bar-filler");
	$("body").prepend($notificationBar);
	$("body").prepend($filler);
	updateFiller();

	return {
		// higher priorities will be shown above lower priorities
		// priority defaults to 0
		// returns a handle which can be used to remove the notification,
		// and should be used to notify when the height of $el changes
		createNotification: function($el, priority) {
			priority = priority || 0;

			var $container = $("<div />").addClass("notification");
			$container.append($el);

			var notification = {priority: priority, $el: $container};
			var inserted = false;
			for(var i=notifications.length-1; !inserted && i>=0; i--) {
				var a = notifications[i];
				if (a.priority >= priority) {
					// insert after the current element
					inserted = true;
					$container.insertAfter(a.$el);
					notifications.splice(i+1, 0, notification);
				}
			}
			if (!inserted) {
				$notificationBar.prepend($container);
				notifications.unshift(notification);
			}
			updateFiller();

			return {
				remove: function() {
					var index = notifications.indexOf(notification);
					if (index === -1) {
						throw "Already removed.";
					}
					notification.$el.remove();
					updateFiller();
					notifications.splice(index, 1);
				},
				onHeightChanged: function() {
					var index = notifications.indexOf(notification);
					if (index === -1) {
						throw "Notification removed.";
					}
					updateFiller();
				}
			};
		}
	};

	function updateFiller() {
		$filler.height($notificationBar.outerHeight(true));
	}
});