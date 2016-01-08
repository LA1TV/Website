require.config({
	baseUrl: "/assets/scripts",
	paths: {
		Clappr: "lib/clappr",
		optional: "app/requirejs-plugins/optional",
		jquery: "lib/jquery",
		ga: "https://www.google-analytics.com/analytics",
		"jquery.ui.widget": "lib/jquery.ui.widget",
		moxie: "lib/moxie",
		plupload: "lib/plupload"
	},
	shim: {
		"lib/bootstrap": ["jquery"],
		"ga": {
			exports: "ga"
		},
		"moxie": {
			exports: "mOxie"
		},
		"plupload": {
			exports: "plupload",
			deps: ['moxie']
		}
	}
});


(function() {
	var startTime = new Date().getTime();
	
	require([
		"lib/es6-promise"
	], function(a) {
		a.polyfill();
		require([
			"app/logger",
			"app/google-analytics",
			"app/version-log",
			"app/error-handler",
			"app/service-worker",
			"lib/bootstrap",
			"app/confirmation-msg",
			"app/custom-accordian",
			"app/custom-form",
			"app/default-ajax-file-upload",
			"app/default-ajax-select",
			"app/default-button-group",
			"app/delete-button",
			"app/page-protect",
			"app/search",
			"app/pages/admin/live-streams-edit-page",
			"app/pages/admin/live-streams-player-page",
			"app/pages/admin/media-edit-page",
			"app/pages/admin/playlist-edit-page",
			"app/pages/admin/users-edit-page"
		], function(logger, googleAnalytics) {
			// everything loaded
			logger.info("Page loaded.");
			googleAnalytics.registerModulesLoadTime("Admin", new Date().getTime() - startTime);
		});
	});	
})();