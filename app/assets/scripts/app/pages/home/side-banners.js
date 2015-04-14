define([
	"jquery",
	"../../device-detection",
	"lib/domReady!"
], function($, DeviceDetection) {
	
	$(".side-banners-container").each(function() {
		var self = this;
		
		if (DeviceDetection.isMobile()) {
			// side banners not shown on mobiles.
			// css will heep them at display:none
			return;
		}
		
		var $sideBannerContainers = $(".side-banner-container");
		var numBanners = $sideBannerContainers.length;
		var numBannersLoaded = 0;
		$sideBannerContainers.each(function() {
			var $container = $(this);
			var bgUrl = $(this).attr("data-bg-url");
			if (bgUrl !== "") {
				$(this).css("background-image", 'url("'+bgUrl+'")');
			}
			
			function updateWidth() {
				$container.width($(window).width());
			}
			updateWidth();
			$(window).resize(updateWidth);
			$("<img />").load(onSideBannerLoaded).attr("src", bgUrl);
		});
		
		function onSideBannerLoaded() {
			if (++numBannersLoaded === numBanners) {
				// all banners loaded
				$(self).animate({opacity: 1}, 300, "swing");
			}
		}
	});

});