define({
	// if uri is not specified then the current uri will be used
	get: function(uri) {
		uri = uri || window.location.href;
		var tmp = uri.split("?");
		var query = "";
		if (tmp.length >= 2) {
			query = tmp[tmp.length-1];
		}
		var params = {};
		if (query.length > 1) {
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
				var pair = vars[i].split("=");
				params[pair[0]] = pair[1];
			}
		}
		return params;
	}
});