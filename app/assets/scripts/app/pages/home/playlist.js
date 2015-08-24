define([
	"jquery",
	"../../device-detection",
	"lib/domReady!"
], function($, DeviceDetection) {

	$(".playlist-element").each(function() {
		var $table = $(this).find(".playlist-table").first();
		// make the entire row a link to the item using the link on the thumbnail
		$table.find("tbody").find("tr").each(function() {
			
			var $imageHolder = null;
			
			// set the thumbnail uri
			$(this).find(".col-thumbnail").each(function() {
				var thumbnailUri = $(this).attr("data-thumbnailuri");
				$imageHolder = $(this).find(".image-container .image-holder");
				$imageHolder.css("background-image", "url('"+thumbnailUri+"')");
			});
			
			var uri = $(this).attr("data-link");
			$(this).click(function(e) {
				window.location = uri;
			});
			
			// on iOS when this was enabled the click event handler would
			// sometimes not get called for some reason
			if (!DeviceDetection.isMobile()) {
				$(this).hover(function() {
					$imageHolder.attr("data-animate", "1");
				}, function() {
					$imageHolder.attr("data-animate", "0");
				});
			}
		});
	});

});