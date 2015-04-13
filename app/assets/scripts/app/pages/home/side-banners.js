define([
	"jquery",
	"lib/domReady!"
], function($) {
	
	$(".side-banners-container").each(function() {
		var self = this;
		
		var $sideBannerContainers = $(".side-banner-container");
		var numBanners = $sideBannerContainers.length;
		var numBannersLoaded = 0;
		$sideBannerContainers.each(function() {
			var bgUrl = $(this).attr("data-bg-url");
			if (bgUrl !== "") {
				$(this).css("background-image", 'url("'+bgUrl+'")');
			}
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