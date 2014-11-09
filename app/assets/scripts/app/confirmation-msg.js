define([
	"jquery",
	"./page-protect",
	"lib/domReady!"
], function($, PageProtect) {

	$("a[data-confirm]").click(function() {
		if (confirm($(this).attr("data-confirm"))) {
			PageProtect.disable();
			return true;
		}
		return false;
	});
});