var $ = require("jquery");
var PromoComponent = require("app/components/promo");
var PageData = require("app/page-data");
var NotificationBar = require("./notification-bar");
var NotificationPriorities = require("./notification-priorities");

var ajaxUri = PageData.get("promoAjaxUri");
var promoComponent = new PromoComponent(ajaxUri);
var handle = NotificationBar.createNotification(promoComponent.getEl(), NotificationPriorities.promo);
$(promoComponent).on("visible hidden", function() {
	handle.onHeightChanged();
});