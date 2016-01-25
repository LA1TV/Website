define([
	"app/logger",
	"jquery",
	"../../components/cookie-compliance-modal",
	"app/cookie-config",
	"imports?jQuery=lib/jquery!lib/jquery.cookie"
], function(Logger, $, CookieComplianceModal, CookieConfig) {
	
	if ($.cookie("closedCookieComplianceModal") === "1") {
		return;
	}
	
	var modal = new CookieComplianceModal();
	$(modal).on("hidden", function() {
		var config = $.extend({}, CookieConfig, {expires: 365*5});
		$.cookie("closedCookieComplianceModal", "1", config)
		Logger.info("User okayed cookies compliance modal.");
	});
	modal.show(true);
});