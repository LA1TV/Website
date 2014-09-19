define(["jquery", "lib/domReady!"], function($) {

	$(".playlist-element").each(function() {
		var $table = $(this).find(".playlist-table").first();
		// make the entire row a link to the item using the link on the thumbnail
		$table.find("tbody").find("tr").each(function() {
			var uri = $(this).find(".col-thumbnail").find("a").attr("href");
			$(this).click(function() {
				window.location = uri;
			});
		});
	});

});