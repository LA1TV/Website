// print out the application version to the console

define(["./page-data", "./logger"], function(PageData, logger) {
	var version = PageData.get("version");
	if (version !== null) {
		console.log("VERSION: "+version);
		logger.debug('Running version "'+version+'".');
	}
	else {
		console.log("VERSION: [Unknown]");
		logger.debug("Running unknown version.");
	}
});