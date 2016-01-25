define([
	"./page-data",
	"lib/log4javascript",
	"./helpers/ajax-helpers"
], function(PageData, log4Javascript, AjaxHelpers) {
	
	log4Javascript.logLog.setQuietMode(true);
	
	var logUri = PageData.get("logUri");
	if (logUri === null) {
		// no uri to log to so disable logging
		return log4Javascript.getNullLogger();
	}
	
	var logger = log4Javascript.getLogger();
	
	// set up the logger
	var ajaxAppender = new log4Javascript.AjaxAppender(logUri, true); // send cookies with request
	var layout = new log4Javascript.HttpPostDataLayout();
	layout.setCustomField("csrf_token", PageData.get("csrfToken")); // pass the csrf token
	var debugId = PageData.get("debugId");
	if (debugId !== null) {
		// if there is a debugId then include it
		layout.setCustomField("debug_id", debugId);
	}
	ajaxAppender.setLayout(layout);
	ajaxAppender.setThreshold(log4Javascript.Level.ALL); // this logger should capture everything
	ajaxAppender.setWaitForResponse(true); // wait for response from each request before sending new one. try to prevent overloading of server if something goes wrong
	// only allow log messages to be sent 5 seconds apart. prevent dos.
	ajaxAppender.setTimed(true);
	ajaxAppender.setTimerInterval(5000);
	
	var headers = AjaxHelpers.getHeaders();
	for (var headerName in headers) {
		if(headers.hasOwnProperty(headerName)){
			ajaxAppender.addHeader(headerName, headers[headerName]);
		}
	}
	logger.addAppender(ajaxAppender);
	
	if (debugId !== null || PageData.get("env") !== "production") {
		// if the user has set a debug id (e.g ?debugId=test),
		// or not in production environment, log to console
		logger.addAppender(new log4Javascript.BrowserConsoleAppender());
	}

	logger.debug("Logger initialised.");
	if (debugId !== null) {
		logger.debug('Using debug ID "'+debugId+'".');
	}

	var version = PageData.get("version");
	if (version !== null) {
		logger.info('Running version "'+version+'".');
	}
	else {
		logger.info("Running unknown version.");
	}
	return logger;
});