define([
	"jquery",
	"../../device-detection",
	"lib/trianglify",
	"lib/domReady!"
], function($, DeviceDetection, Trianglify) {
	
	$(".side-banners-container-container").each(function() {
		var self = this;
		
		if (DeviceDetection.isMobile()) {
			// side banners not shown on mobiles.
			// css will heep them at display:none
			return;
		}
		
		var $sideBannerContainers = $(".side-banner-container");
		var numBanners = $sideBannerContainers.length;
		var numBannersLoaded = 0;
		var trianglifyPng = null;
		var getTrianglifyPng = function() {
			if (!trianglifyPng) {
				var xColours = ["#fff5f0","#fee0d2","#fcbba1","#fc9272","#fb6a4a","#ef3b2c","#cb181d","#a50f15","#67000d"];
				var yColours = ["#f7fbff","#deebf7","#c6dbef","#9ecae1","#6baed6","#4292c6","#2171b5","#08519c","#08306b"];
				if (Math.random() < 0.5) {
					xColours = xColours.reverse();
				}
				if (Math.random() < 0.5) {
					yColours = yColours.reverse();
				}
				var pattern = Trianglify({
					width: 1000, // should be more than enough
					height: 1400, // matches height of user uploaded side banners,
					cell_size: 75,
					variance: 0.8,
					seed: Math.floor(Date.now() / 60000), // new pattern every minute
					x_colors: xColours,
					y_colors: yColours
				});
				// returns a data URI with the PNG data in base64 encoding
				trianglifyPng = pattern.png();
			}
			return trianglifyPng;
		};
		$sideBannerContainers.each(function() {
			var $container = $(this);
			var bgUrl = $(this).attr("data-bg-url");
			if (bgUrl === "") {
				// if no background fill image is provided use a generated one
				bgUrl = getTrianglifyPng();
			}
			$(this).css("background-image", 'url("'+bgUrl+'")');
			
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