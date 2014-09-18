define({
	get: function() {
		var params = {};
		if (window.location.search.length > 1) {
			var query = query = window.location.search.substring(1);
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
				var pair = vars[i].split("=");
				params[pair[0]] = pair[1];
			}
		}
		return params;
	}
});