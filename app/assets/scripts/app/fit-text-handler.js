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
	
	// TODO there is no way to unregister before an element which has this applied is removed
	// from the DOM.
	return {
		register: registerFitText
	}
});