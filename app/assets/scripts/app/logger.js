define([
	"./page-data",
	"lib/log4javascript"
], function(PageData, log4Javascript) {
	
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
	logger.addAppender(ajaxAppender);
	logger.debug("Logger initialised.");
	return logger;
});