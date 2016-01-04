define([
	"../../components/promo",
	"../../page-data",
	"./notification-bar",
	"./notification-priorities"
], function(PromoComponent, PageData, NotificationBar, NotificationPriorities) {
	var ajaxUri = PageData.get("promoAjaxUri");
	var promoComponent = new PromoComponent(ajaxUri);
	var handle = NotificationBar.createNotification(promoComponent.getEl(), NotificationPriorities.promo);
	$(promoComponent).on("visible hidden", function() {
		handle.onHeightChanged();
	});
});