var $ = require("jquery");
require("app/pages/home/playlist-element.css");

$(document).ready(function() {
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
		});
	});
});