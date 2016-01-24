var $ = require("jquery");
require("imports?jQuery=lib/jquery!lib/jquery.fittext");
require("./fit-text.css");

var registerFitText = null;

$(document).ready(function() {
	$(".fit-text").each(function() {
		registerFitText($(this).first());
	});
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
module.exports = {
	register: registerFitText
}