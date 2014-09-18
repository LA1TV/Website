define(["jquery"], function($) {

	msg = null;
	window.onbeforeunload = null;
	
	var unloadFunction = function() {
		return msg;
	};

	var PageProtect = {
		enable: function(a) {
			msg = a;
			window.onbeforeunload = unloadFunction;
		},
		disable: function() {
			msg = null;
			window.onbeforeunload = null;
		}
	};
	
	$(document).ready(function() {
		$('a[data-disablepageprotect="1"]').click(function() {
			PageProtect.disable();
		});
	});
	
	return PageProtect;
});