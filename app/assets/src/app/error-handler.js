define(["app/logger"], function(logger) {
	
	window.onerror = function(errorMsg, url, lineNumber) {
		logger.error("Error on line "+lineNumber+" in \""+url+"\". Error msg: "+errorMsg);
	};
	
});