var Promise = require("lib/es6-promise").Promise;

module.exports = {
	// based on https://gist.github.com/bradberger/9d3ba85c63c21b317f5d#file-script-js
	// load the file at url async and resolve promise when done
	load: function(url) {

		if(Array.isArray(url)) {
			var self = this, prom = [];
			url.forEach(function(item) {
				prom.push(self.script(item));
			});
			return Promise.all(prom);
		}

		return new Promise(function(resolve, reject) {
			var r = false,
				t = document.getElementsByTagName("script")[0],
				s = document.createElement("script");

			s.type = "text/javascript";
			s.src = url;
			s.async = true;
			s.onload = s.onreadystatechange = function () {
				if (!r && (!this.readyState || this.readyState == "complete")) {
					r = true;
					resolve(this);
				}
			};
			s.onerror = s.onabort = reject;
			t.parentNode.insertBefore(s, t);
		});
	}
};