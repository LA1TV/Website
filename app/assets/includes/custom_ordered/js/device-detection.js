var DeviceDetection = null;

(function() {
	var result = null;
	
	DeviceDetection = {
		isMobile: function() {
			if (result !== null) {
				return result;
			}
			// http://stackoverflow.com/a/3540295/1048589
			return result = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
		}
	}
})();