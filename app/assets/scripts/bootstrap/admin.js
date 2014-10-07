require.config({
	baseUrl: "/assets/scripts",
	paths: {
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

require([
	"lib/bootstrap",
	"app/google-analytics",
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
	"app/pages/admin/media-edit-page",
	"app/pages/admin/playlist-edit-page",
	"app/pages/admin/users-edit-page"
], function() {
	// everything loaded
});