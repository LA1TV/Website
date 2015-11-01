define([
	"jquery",
	"lib/tether",
	"lib/domReady!"
], function($, Tether) {

	$(".navbar .search-btn").first().each(function() {

		var self = this;
		var $self = $(self);
		var $body = $('body');

		var $searchDialog = null;
		var $searchInput = null;

		var visible = false;

		$body.click(function(e) {
			if ($(e.target).closest($self).length === 0 && $(e.target).closest($searchDialog).length === 0) {
				// a click outside of the search button or dialog
				hide();
			}
		});

		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				// escape key pushed
				hide();
			}
		});

		$(this).click(function() {
			toggle();
		});

		function toggle() {
			visible ? hide() : show();
		}

		function show() {
			if (visible) {
				return;
			}
			visible = true;
			initSearchDialog();
			$searchDialog.removeClass("hidden");
			$searchInput.focus();
		}

		function hide() {
			if (!visible) {
				return;
			}
			visible = false;
			$searchDialog.addClass("hidden");
		}

		function initSearchDialog() {
			if (!$searchDialog) {
				$searchDialog = $("<div />").addClass("search-dialog");
				
				var $inputContainer = $("<div />").addClass("search-input-container");
				$searchInput = $("<input />").addClass("form-control search-input").attr("autocomplete", "off").attr("placeholder", "Search...");
				$inputContainer.append($searchInput);
				$searchDialog.append($inputContainer);
				var $resultsContainer = $("<div />").addClass("results-container");
				$searchDialog.append($resultsContainer);

				// Temporary
				for (var i=0; i<20; i++) {
					$resultsContainer.append(buildResult());
				}

				$body.append($searchDialog);
				new Tether({
					element: $searchDialog,
					target: $self,
					attachment: 'top right',
					targetAttachment: 'bottom right'
				});
			}
			return $searchDialog;
		}

		function buildResult() {
			return $("<div />").addClass("search-result").text("Result");
		}
	});
});