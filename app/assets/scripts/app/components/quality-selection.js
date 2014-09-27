define(["jquery", "../../lib/jquery.cookie", "../cookie-config"], function($, cookieConfig) {

	var QualitySelectionComponent = function() {
		
		var self = this;
		
		this.getEl = function() {
			return $container;
		};
		
		this.getChosenQualityId = function() {
			if (chosenQuality === null) {
				throw "No quality has been chosen. There probably aren't any available.";
			}
			return chosenQuality.id;
		};
		
		this.hasQualities = function() {
			return availableQualities.length > 0;
		};
		
		this.setQuality = function(qualityId, userChosen) {
			var userChosen = userChosen || false;
			var requestedQuality = getQualityFromId(qualityId);
			if (requestedQuality === null) {
				throw "The requested quality could not be found.";
			}
			if (chosenQuality !== null && requestedQuality.id === chosenQuality.id) {
				// quality hasn't changed
				return;
			}
			chosenQuality = requestedQuality;
			if (userChosen) {
				setActivelyChosenQuality(requestedQuality);
			}
			render();
			$(self).triggerHandler("chosenQualityChanged");
		};
		
		this.setAvailableQualities = function(qualities) {
			availableQualities = qualities;
			
			if (!self.hasQualities()) {
				chosenQuality = null;
			}
			else {
				// if the previously quality id that was actively chosen is still available use that.
				// otherwise if pevious quality id that was chosen is still available use that.
				// otherwise pick the first quality.
				var foundActivelyChosen = false;
				var foundChosen = false;
				var theActivelyChosenQuality = getActivelyChosenQuality();
				var activelyChosenQualityId = theActivelyChosenQuality !== null ? theActivelyChosenQuality.id : null;
				var chosenQualityId = chosenQuality !== null ? chosenQuality.id : null;
				for (var i=0; i<availableQualities.length; i++) {
					var quality = availableQualities[i];
					if (quality.id === activelyChosenQualityId) {
						foundActivelyChosen = true;
						break;
					}
					else if (quality.id === chosenQualityId) {
						foundChosen = true;
					}
				}
				if (foundActivelyChosen) {
					chosenQuality = theActivelyChosenQuality;
				}
				else if (foundChosen) {
					// nothing to do as it's already chosen.
				}
				else {
					// pick the first one.
					chosenQuality = availableQualities[0];
				}
			}
			render();
			$(self).triggerHandler("qualitiesChanged");
		};
		
		var $container = $("<div />").addClass("quality-selection-component").hide();
		var $btnGroup = $("<div />").addClass("btn-group");
		var $button = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs dropdown-toggle").attr("data-toggle", "dropdown");
		var $buttonTxt = $("<span />");
		var $buttonCaret = $("<span />").addClass("caret");
		$button.append($buttonTxt);
		$button.append($buttonCaret);
		var $dropdownMenu = $("<ul />").addClass("dropdown-menu").attr("role", "menu");
		
		$btnGroup.append($button);
		$btnGroup.append($dropdownMenu);
		$container.append($btnGroup);
		
		var availableQualities = [];
		// the quality that should be used and is currently represented by the component
		var chosenQuality = null;
		// the quality that was last chosen by the user. could be one that is currently unavailable
		var activelyChosenQuality = null;
		var activelyChosenQualityIdFromCookie = $.cookie("chosenQualityId") || null;
		if (activelyChosenQualityIdFromCookie !== null) {
			activelyChosenQualityIdFromCookie = parseInt(activelyChosenQualityIdFromCookie);
		}
		
		render();
		
		function setActivelyChosenQuality(quality) {
			activelyChosenQuality = quality;
			$.cookie("chosenQualityId", quality.id, cookieConfig); 
		}
		
		function getActivelyChosenQuality() {
			if (activelyChosenQuality !== null) {
				return activelyChosenQuality;
			}
			if (activelyChosenQualityIdFromCookie === null) {
				return null;
			}
			// lookup quality and return that. will be null if not found
			activelyChosenQuality = getQualityFromId(activelyChosenQualityIdFromCookie);
			return activelyChosenQuality;
		}
		
		function getQualityFromId(id) {
			for (var i=0; i<availableQualities.length; i++) {
				var quality = availableQualities[i];
				if (quality.id === id) {
					return quality;
				}
			}
			return null;
		}
		
		function render() {
			if (!self.hasQualities()) {
				$container.hide();
			}
			else {
				$dropdownMenu.empty();
				if (availableQualities.length === 1) {
					var $item = $("<span />").addClass("item").text("No other qualities available.");
					$dropdownMenu.append($("<li />").append($item));
				}
				else {
					for (var i=0; i<availableQualities.length; i++) {
						var quality = availableQualities[i];
						if (quality.id === chosenQuality.id) {
							continue;
						}
						var $item = $("<span />").addClass("item item-hover").text(quality.name);
						(function(id) {
							$item.click(function() {
								self.setQuality(id, true);
							});
						})(quality.id);
						$dropdownMenu.append($("<li />").append($item));
					}
				}
				$buttonTxt.text("Quality: "+chosenQuality.name+" ");
				$container.show();
			}
		}
	};
	return QualitySelectionComponent;
});