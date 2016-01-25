define([
	"jquery",
	"./page-protect",
], function($, PageProtect) {

	$(document).ready(function() {
		$("a[data-confirm]").click(function() {
			if (confirm($(this).attr("data-confirm"))) {
				PageProtect.disable();
				return true;
			}
			return false;
		});
	});
});