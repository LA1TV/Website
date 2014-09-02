$(document).ready(function() {
	$(".fit-text").each(function() {
		$(this).fitText($(this).attr("data-compressor"));
	});
});