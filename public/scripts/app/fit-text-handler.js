define(["jquery", "lib/jquery.fittext", "lib/domReady!"], function($) {

	var registerFitText = null;

	$(".fit-text").each(function() {
		registerFitText($(this).first());
	});
	
	registerFitText = function($el) {
		if ($el.attr("data-fittextregistered") === "1") {
			return;
		}
		$el.attr("data-fittextregistered", "1");
		$el.fitText($el.attr("data-compressor"));
	};
	
	return {
		register: registerFitText
	}
});