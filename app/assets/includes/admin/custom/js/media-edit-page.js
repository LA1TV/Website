$(document).ready(function() {
	
	var disableFn = function($panel, $enabledInput) {
		var $disabledContainer = $panel.find(".disabled-container");
		var $enabledContainer = $panel.find(".enabled-container");
		$enabledContainer.hide();
		$enabledContainer.find('[data-virtualform]').attr("disabled", true);
		$enabledInput.val("0");
		$disabledContainer.show();
	};
	
	var enableFn = function($panel, $enabledInput) {
		var $disabledContainer = $panel.find(".disabled-container");
		var $enabledContainer = $panel.find(".enabled-container");
		$disabledContainer.hide();
		$enabledContainer.find('[data-virtualform]').prop("disabled", false);
		$enabledInput.val("1");
		$enabledContainer.show();
	}
	
	var handler = function() {
		var $panel = $(this).first();
		var $enabledInput = $panel.find('.enabled-input input').first();
		$enabledInput.val() === "1" ? enableFn($panel, $enabledInput) : disableFn($panel, $enabledInput);
		
		$(this).find(".disabled-container .enable-button").click(function(){enableFn($panel, $enabledInput);});
		$(this).find(".enabled-container .disable-button").click(function() {
			if (!confirm("Are you sure you want to remove this?")) {
				return;
			}
			disableFn($panel, $enabledInput);
		});
	};
	
	$(".page-media-edit .vod-panel").each(handler);
	$(".page-media-edit .live-stream-panel").each(handler);
	
});