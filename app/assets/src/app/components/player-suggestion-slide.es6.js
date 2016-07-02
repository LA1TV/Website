var $ = require("jquery");
var AjaxHelpers = require("../helpers/ajax-helpers");
var PageData = require("../page-data");
var css = require("./player-suggestion-slide.scss");

var speed = 100;

function PlayerSuggestionSlide(coverUri, ajaxUrl, onWatchAgainClicked, openInNewWindow) {
	this._coverUri = coverUri;
	this._ajaxUrl = ajaxUrl;
	this._onWatchAgainClicked = onWatchAgainClicked;

	this._$el = null;
	this._$topLayer = null;
	this._$suggestion = null;
	this._$background = null;
	this._$leftArrow = null;
	this._$rightArrow = null;
	this._$artContainer = null;
	this._$title = null;
	this._$restartButton = null;
	this._$currentArt = null;
	this._xhr = null;
	this._retryTimer = null;
	this._items = null;
	this._currentItemIndex = 0;
	this._animating = false;
	this._buildEl();

	this._$background.css({"background-image": "url("+coverUri+")"});

	this._$restartButton.click(() => {
		this._onWatchAgainClicked();
	});
	this._$leftArrow.click(() => {
		this._$leftArrow.blur();
		this._onLeftClicked();
	});
	this._$rightArrow.click(() => {
		this._$rightArrow.blur();
		this._onRightClicked();
	});
	this._$suggestion.click(() => {
		var uri = this._items[this._currentItemIndex].uri;
		if (openInNewWindow) {
			window.open(uri);
		}
		else {
			window.location = uri;
		}
	});
	this._$topLayer.hover(() => {
		this._$background.addClass(css.darken);
	}, () => {
		this._$background.removeClass(css.darken);
	});

	this._makeRequest();
}

PlayerSuggestionSlide.prototype.getEl = function() {
	return this._$el;
};

PlayerSuggestionSlide.prototype.destroy = function() {
	if (this._xhr) {
		updateXHR.abort();
	}
	if (this._retryTimer !== null) {
		clearTimeout(this._retryTimer);
	}
};

PlayerSuggestionSlide.prototype._makeRequest = function() {
	this._xhr = $.ajax(this._ajaxUrl, {
		cache: false,
		dataType: "json",
		headers: AjaxHelpers.getHeaders(),
		data: {
			csrf_token: PageData.get("csrfToken")
		},
		type: "POST"
	}).done((data, textStatus, jqXHR) => {
		this._xhr = null;
		if (jqXHR.status === 200) {
			if (data.items.length === 0) {
				// the component needs 1 or more items to function
				return;
			}
			this._items = data.items;
			if (this._items.length > 1) {
				this._$topLayer.addClass(css.multipleItems);
			}
			this._animate(true);
			this._$el.attr("data-ready", "1");
			setTimeout(() => {
				this._$el.attr("data-loaded", "1");
			}, 0);
		}
		else {
			onError();
		}
	}).fail(() => {
		this._xhr = null;
		onError();
	});

	var onError = () => {
		// retry in 15 seconds
		this._retryTimer = setTimeout(() => {
			this._retryTimer = null;
			this._makeRequest();
		}, 15000);
	};
		
};

PlayerSuggestionSlide.prototype._onLeftClicked = function() {
	if (this._animating) {
		return;
	}
	this._currentItemIndex = (this._currentItemIndex+this._items.length-1)%this._items.length;
	this._animate();
};

PlayerSuggestionSlide.prototype._onRightClicked = function() {
	if (this._animating) {
		return;
	}
	this._currentItemIndex = (this._currentItemIndex+1)%this._items.length;
	this._animate();
};

PlayerSuggestionSlide.prototype._animate = function(instant) {
	if (this._animating) {
		return;
	}
	this._animating = true;

	var item = this._items[this._currentItemIndex];

	if (instant) {
		if (this._$currentArt) {
			this._$currentArt.remove();
		}
		this._$currentArt = this._buildArt(item.coverArtUri);
		this._$artContainer.append(this._$currentArt);
		this._$title.text(item.title);
		this._animating = false;
		return;
	}

	var animationsRunning = 0;

	// append new art
	var $art = this._buildArt(item.coverArtUri).css({opacity: 0});
	var $artToRemove = this._$currentArt;
	this._$currentArt = $art;
	this._$artContainer.append($art);
	// animate in new art over previous
	animationsRunning++;
	$art.animate({
		opacity: 1
	}, speed*2, () => {
		$artToRemove.remove();
		onAnimationComplete();
	});

	// animate out title
	animationsRunning++;
	this._$title.animate({
		opacity: 0
	}, speed, () => {
		// update title
		this._$title.text(item.title);
		// animate in new title
		this._$title.animate({
			opacity: 1
		}, speed, () => {
			onAnimationComplete();
		});
	});

	var self = this;
	function onAnimationComplete() {
		if (--animationsRunning !== 0) {
			return;
		}
		self._animating = false;
	}
};

PlayerSuggestionSlide.prototype._buildArt = function(imgUrl) {
	return $("<img />").addClass(css.art).attr("src", imgUrl);
}

PlayerSuggestionSlide.prototype._buildEl = function() {
	var $el = $("<div />").addClass(css.suggestionsOverlay);
	var $background = $("<div />").addClass(css.background);
	var $recommendedTitle = $("<div />").addClass(css.recommendedTitle);
	var $recommendedTitleTitle = $("<h3 />").addClass(css.recommendedTitleTitle).text("Recommended For You");
	$recommendedTitle.append($recommendedTitleTitle);
	var $topLayer = $("<div />").addClass(css.topLayer);
	var $leftArrowContainer = $("<div />").addClass(css.arrowContainer).addClass(css.leftArrowContainer);
	var $rightArrowContainer = $("<div />").addClass(css.arrowContainer).addClass(css.rightArrowContainer);
	var $leftArrow = $("<button />").addClass(css.arrow).addClass(css.leftArrow).attr("tabindex", "0").text("<");
	var $rightArrow = $("<button />").addClass(css.arrow).addClass(css.rightArrow).attr("tabindex", "0").text(">");
	$leftArrowContainer.append($leftArrow);
	$rightArrowContainer.append($rightArrow);
	var $suggestion = $("<button />").addClass(css.suggestion).attr("tabindex", "0");
	var $artContainer = $("<div />").addClass(css.artContainer).addClass("embed-responsive embed-responsive-16by9");
	var $filler = $("<div />").addClass("embed-responsive-item");
	$artContainer.append($filler);
	var $title = $("<div />").addClass(css.title);
	$suggestion.append($artContainer);
	$suggestion.append($title);
	$topLayer.append($leftArrowContainer);
	$topLayer.append($suggestion);
	$topLayer.append($rightArrowContainer);
	var $restartButton = $("<button />").addClass(css.restartButton).attr("tabindex", "0");
	$restartButton.append($("<span />").addClass("glyphicon glyphicon-repeat"));
	$restartButton.append($("<span />").text(" Watch Again"));
	$el.append($background);
	$el.append($recommendedTitle);
	$el.append($topLayer);
	$el.append($restartButton);

	this._$el = $el;
	this._$topLayer = $topLayer;
	this._$suggestion = $suggestion;
	this._$background = $background;
	this._$leftArrow = $leftArrow;
	this._$rightArrow = $rightArrow;
	this._$artContainer = $artContainer;
	this._$title = $title;
	this._$restartButton = $restartButton;
};

module.exports = PlayerSuggestionSlide;