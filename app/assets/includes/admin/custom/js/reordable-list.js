// handles all .reordable-list

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	$(".reordable-list").each(function() {
		register($(this).first());
	});
	
	function register($container) {
	
		
	};
	
});