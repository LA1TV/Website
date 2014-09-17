function e(str) {
	return htmlEncode(str);
}

function htmlEncode(str) {
	return $("<div />").text(str).html();
}