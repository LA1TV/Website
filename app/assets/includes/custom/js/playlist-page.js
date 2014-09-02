$(document).ready(function() {
	
	$(".page-playlist").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container").each(function() {
			var $player = $(this).find(".player").first();
			var $video = $player.find("video").first();
			videojs($video[0], {
				width: "100%",
				height: "100%",
				controls: true,
				preload: "auto",
				autoplay: $player.attr("data-autoplay") === "1",
				poster: $player.attr("data-coveruri"),
				loop: false
			}, function() {
				// called when player loaded.
			});
		});	
	});
	
});