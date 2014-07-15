var pageProtect = null;

(function() {
	msg = null;
	
	window.onbeforeunload = function() {
		return msg;
	}

	pageProtect = {
		enable: function(a) {
			msg = a;
		},
		disable: function() {
			msg = null;
		}
	};
	
	$(document).ready(function() {
		$('a[data-disablepageprotect="1"]').click(function() {
			pageProtect.disable();
		});
	});
	
})();