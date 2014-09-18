define([
	"jquery",
	"lib/domReady!"
], function($) {

	$(".page-player").first().each(function() {
		
		var $pageContainer = $(this).first();
		
		var $headingContainer = $pageContainer.find(".heading-container");
		var $playerContainer = $pageContainer.find(".player-container");
		
		$playerContainer.width("100%");
		
		$(window).resize(render);
		render();
		
		function render() {
			var containerHeight = $pageContainer.innerHeight();
			var headingHeight = $headingContainer.outerHeight(true);
			$playerContainer.height(Math.max(containerHeight - headingHeight, 0));
			$playerContainer.show();
		}
		
	});
	
});