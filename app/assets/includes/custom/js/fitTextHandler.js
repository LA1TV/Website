var registerFitText = null;

$(document).ready(function() {
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
});