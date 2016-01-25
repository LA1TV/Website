var $ = require("jquery");
var PageData = require("app/page-data");
var AjaxHelpers = require("app/helpers/ajax-helpers");
require("./promo.css");

var PromoComponent = function(ajaxUri) {
	
	var self = this;	
	
	this.getEl = function() {
		return $el;
	};
	
	// contains array of {id, name, scheduledPublishTime, uri}
	var liveItems = [];
	
	var queuedItemIndex = null;
	var currentItemIndex = null;
	var animationsRunning = 0;
	var visible = false;
	var itemVisible = false;
	var aniDuration = 600;
	var timerId = null;
	var currentUri = null;
	
	var $el = $("<div />").addClass("promo").hide();
	var $container = $("<div />").addClass("container clearfix");
	var $liveLft = $("<div />").addClass("item item-lft live-txt").text("LIVE NOW");
	var $liveRgt = $("<div />").addClass("item item-rgt live-txt").text("LIVE NOW");
	var $liveItem = $("<div />").addClass("item live-item");
	
	$el.click(function() {
		if (currentUri !== null) {
			window.location = currentUri;
		}
	});
	
	$container.append($liveLft);
	$container.append($liveRgt);
	$container.append($liveItem);
	$el.append($container);
	
	update();
	
	// update items from server
	function update() {
		$.ajax(ajaxUri, {
			cache: false,
			dataType: "json",
			headers: AjaxHelpers.getHeaders(),
			data: {
				csrf_token: PageData.get("csrfToken")
			},
			type: "POST"
		}).always(function(data, textStatus, jqXHR) {
			if (jqXHR.status === 200) {
				var changed = true;
				if (data.items.length === liveItems.length) {
					changed = false;
					for (var i=0; i<data.items.length; i++) {
						var newLiveItem = data.items[i];
						var liveItem = liveItems[i];
						if (liveItem.id !== newLiveItem.id) {
							changed = true;
							break;
						}
					}
				}
				if (changed) {
					liveItems = data.items;
					queuedItemIndex = null;
					currentItemIndex = null;
					animate();
				}
				
			}
			setTimeout(update, 12000);
		});
	}
	
	// switch items
	function animate() {
		if (liveItems.length > 0) {
			queuedItemIndex = queuedItemIndex !== null ? (queuedItemIndex+1) % liveItems.length : 0;	
		}
		else {
			queuedItemIndex = null;
		}
		render();
		if (timerId !== null) {
			clearTimeout(timerId);
		}
		timerId = setTimeout(animate, 4500);
	}
	
	function render() {
		if (animationsRunning !== 0) {
			return;
		}
		
		if (queuedItemIndex === null) {
			if (visible) {
				$el.hide();
				$liveItem.text("");
				currentUri = null;
				currentItemIndex = null;
				$(self).triggerHandler("hidden");
				visible = false;
			}
		}
		else if (queuedItemIndex !== null) {
			
			if (currentItemIndex !== null && queuedItemIndex === currentItemIndex) {
				return;
			}
		
			if (itemVisible && visible) {
				animationsRunning++;
				$liveItem.fadeOut({
					duration: aniDuration,
					easing: "linear",
					done: animationCompleted
				});
				itemVisible = false;
			}
			else {
				liveItem = liveItems[queuedItemIndex];
				$liveItem.text(liveItem.name);
				currentUri = liveItem.uri;
				if (!visible) {
					$el.show();
					visible = true;
					$(self).triggerHandler("visible");
				}
				else {
					animationsRunning++;
					$liveItem.fadeIn({
						duration: aniDuration,
						easing: "linear",
						done: animationCompleted
					});
				}
				itemVisible = true;
				currentItemIndex = queuedItemIndex;
			}
		}
	}
	
	function animationCompleted() {
		animationsRunning--;
		render();
	}
};

module.exports = PromoComponent;