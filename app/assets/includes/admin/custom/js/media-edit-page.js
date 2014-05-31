$(document).ready(function() {
	
	var disableFn = function($panel) {
		var $disabledContainer = $panel.find(".disabled-container");
		var $enabledContainer = $panel.find(".enabled-container");
		$enabledContainer.hide();
		$enabledContainer.find('[data-virtualform]').attr("disabled", true);
		$disabledContainer.show();
	};
	
	var enableFn = function($panel) {
		var $disabledContainer = $panel.find(".disabled-container");
		var $enabledContainer = $panel.find(".enabled-container");
		$disabledContainer.hide();
		$enabledContainer.find('[data-virtualform]').prop("disabled", false);
		$enabledContainer.show();
	}
	
	var handler = function() {
		var $panel = $(this).first();
		disableFn($panel);
		$(this).find(".disabled-container .enable-button").click(function(){enableFn($panel);});
		$(this).find(".enabled-container .disable-button").click(function() {
			if (!confirm("Are you sure you want to remove this?")) {
				return;
			}
			disableFn($panel);
		});
	};
	
	$(".page-media-edit .vod-panel").each(handler);
	$(".page-media-edit .live-stream-panel").each(handler);
	
});