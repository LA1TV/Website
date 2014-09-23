define([
	"jquery",
	"../../components/promo",
	"lib/domReady!"
], function($, PromoComponent) {

	$(".promo-container").each(function() {
		var ajaxUri = $(this).attr("data-ajaxuri");
		var promoComponent = new PromoComponent(ajaxUri);
		$(this).append(promoComponent.getEl());
		$("body").prepend(promoComponent.getFillerEl());
	});

});