define(["jquery", "../page-data"], function($, PageData) {

	var PromoComponent = function(ajaxUri) {
		
		var self = this;	
		
		this.getEl = function() {
			return $el;
		};
		
		this.getFillerEl = function() {
			return $filler;
		}
		
		// contains array of {id, name, scheduledPublishTime, uri}
		var items = [];
		
		var queuedItemIndex = null;
		var currentItemIndex = null;
		var animationsRunning = 0;
		var visible = false;
		var itemVisible = false;
		var aniDuration = 600;
		var timerId = null;
		var currentUri = null;
		
		var $el = $("<div />").addClass("promo").hide();
		var $filler = $("<div />");
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
			jQuery.ajax(ajaxUri, {
				cache: false,
				dataType: "json",
				data: {
					csrf_token: PageData.get("csrfToken")
				},
				type: "POST"
			}).always(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200) {
					var changed = true;
					if (data.items.length === items.length) {
						changed = false;
						for (var i=0; i<data.items.length; i++) {
							var newItem = data.items[i];
							var item = items[i];
							if (item.id !== newItem.id) {
								changed = true;
								break;
							}
						}
					}
					if (changed) {
						items = data.items;
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
			if (items.length > 0) {
				queuedItemIndex = queuedItemIndex !== null ? (queuedItemIndex+1) % items.length : 0;	
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
					$filler.height(0);
					$liveItem.text("");
					currentUri = null;
					currentItemIndex = null;
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
					item = items[queuedItemIndex];
					$liveItem.text(item.name);
					currentUri = item.uri;
					if (!visible) {
						$el.show();
						visible = true;
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
					$filler.height(Math.max($el.outerHeight(true)-15, 0));
					currentItemIndex = queuedItemIndex;
				}
			}
		}
		
		function animationCompleted() {
			animationsRunning--;
			render();
		}
	};
	
	return PromoComponent;
});