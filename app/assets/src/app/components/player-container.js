var $ = require("jquery");
var QualitySelectionComponent = require("./quality-selection");
var ShareModal= require("./share-modal");
var AlertModal = require("./alert-modal");
var PlayerSuggestionSlide = require("./player-suggestion-slide");
var PlayerController = require("../player-controller");
var PageData = require("app/page-data");
var SynchronisedTime = require("app/synchronised-time");
require("imports?jQuery=lib/jquery!lib/jquery.hotkeys");
require("imports?jQuery=lib/jquery!lib/jquery.dateFormat");
require("./player-container.css");
	
// registerWatchingUri and registerLikeUri may be null to disable these features
// bottom bar mode can be "full", "compact" or "none"
var PlayerContainer = function(playerInfoUri, registerWatchingUri, registerLikeUri, recommendationsUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, bottomBarMode, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay, muted) {
	muted = !!muted;
	var self = this;

	this.getEl = function() {
		return $container;
	};
	
	this.updateDimensions = function() {
		updatePlayerComponentSize();
	};
	
	this.onLoaded = function(callback) {
		if (loaded) {
			callback();
		}
		else {
			$(self).on("loaded", callback);
		}
	};
	
	this.getPlayerController = function() {
		return playerController;
	};

	if (bottomBarMode !== "full" && bottomBarMode !== "compact" && bottomBarMode !== "none") {
		throw "Invalid bottom bar mode.";
	}
	
	var $container = $("<div />").addClass("player-container");
	if (embedded) {
		$container.addClass("embedded-player-container");
	}
	var $playerOuter = $("<div />").addClass("player-outer");
	if (!embedded) {
		$playerOuter.addClass("embed-responsive-item");
		var $playerOuterOuter = $("<div />").addClass("player-outer-outer embed-responsive embed-responsive-16by9");
		$playerOuterOuter.append($playerOuter);
		$container.append($playerOuterOuter);
	}
	else {
		$container.append($playerOuter);
	}
	var $playerWrapper = $("<div />").addClass("player-wrapper");
	$playerOuter.append($playerWrapper);
	var $bottomContainer = $("<div />").addClass("bottom-container clearfix");
	var $count1ItemContainer = $("<div />").addClass("item-container hide");
	var $count1 = $("<div />").addClass("view-count");
	$count1ItemContainer.append($count1);
	var $count2ItemContainer = $("<div />").addClass("item-container new-line hide");
	var $count2 = $("<div />").addClass("view-count");
	$count2ItemContainer.append($count2);
	var $likeButtonItemContainer = null;
	var $likeButton = null;
	var $likeButtonGlyphicon = null;
	var $likeButtonTxt = null;
	if (registerLikeUri) {
		// likes enabled
		var $likeButtonItemContainer = $("<div />").addClass("item-container right hide");
		var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
		var $likeButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-thumbs-up");
		var $likeButtonTxt = $("<span />");
		$likeButton.append($likeButtonGlyphicon);
		$likeButton.append($likeButtonTxt);
		$likeButtonItemContainer.append($likeButton);
	}
	var $shareButtonItemContainer = $("<div />").addClass("item-container right hide");
	var $shareButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
	var $shareButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-share");
	var $shareButtonTxt = $("<span />").text(" Share");
	$shareButtonItemContainer.append($shareButton);
	var $overrideButtonItemContainer = $("<div />").addClass("item-container");
	var $overrideButton = $("<button />").attr("type", "button").addClass("override-button btn btn-default btn-xs");
	$overrideButtonItemContainer.append($overrideButton);
	var $broadcastTimeContainer = $("<div />").addClass("item-container right new-line hide");
	var $broadcastTime = $("<div />").addClass("broadcast-on-msg");
	$broadcastTimeContainer.append($broadcastTime);
	var $playerComponent = null;
	$shareButton.append($shareButtonGlyphicon);
	$shareButton.append($shareButtonTxt);
	var $qualitySelectionItemContainer = $("<div />").addClass("item-container right");
	
	var $rightGroupContainer = $("<div />").addClass("right");

	var suggestionSlide = null;

	$bottomContainer.append($count1ItemContainer);
	$bottomContainer.append($overrideButtonItemContainer);
	$rightGroupContainer.append($qualitySelectionItemContainer);
	$rightGroupContainer.append($likeButtonItemContainer);
	$rightGroupContainer.append($shareButtonItemContainer);
	$bottomContainer.append($rightGroupContainer);
	$bottomContainer.append($count2ItemContainer);
	$bottomContainer.append($broadcastTimeContainer);
	
	var loaded = false;
	var responsive = !embedded;
	var pleaseLoginModal = null;
	
	var qualitySelectionComponent = new QualitySelectionComponent();
	if (!placeQualitySelectionComponentInPlayer) {
		// quality selection component should be added to bottom row.
		$(qualitySelectionComponent).on("qualitiesChanged", function() {
			renderQualitySelectionComponent();
		});
		renderQualitySelectionComponent();
	}
	$qualitySelectionItemContainer.append(qualitySelectionComponent.getEl());
	
	
	var playerController = new PlayerController(playerInfoUri, registerWatchingUri, registerLikeUri, qualitySelectionComponent, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay, embedded, false, muted);
	$(playerController).on("playerComponentElAvailable", function() {
		$playerComponent = playerController.getPlayerComponentEl();
		$playerWrapper.append($playerComponent);
		if (bottomBarMode !== "none") {
			$container.append($bottomContainer);
		}
		renderOverrideMode();
		renderOverrideButton();
		renderShareButton();
		if (enableAdminOverride) {
			$overrideButton.click(function() {
				playerController.enableOverrideMode(!playerController.getOverrideModeEnabled());
			});
			// toggle override mode with alt+o hotkey
			// would be better to bind it to $container but this didn't seem to work.
			$(document).bind('keydown', 'alt+o', function() {
				playerController.enableOverrideMode(!playerController.getOverrideModeEnabled());
			});
		}
		updatePlayerComponentSize();
		loaded = true;
		$(self).triggerHandler("loaded");
	});
	
	$(playerController).on("embedDataAvailable", function() {
		var shareModal = new ShareModal(playerController.getEmbedData());
		$shareButton.click(function() {
			// pause video/stream on modal load
			playerController.pause();
			shareModal.show(true);
		});
		$shareButtonItemContainer.removeClass("hide");
	});
	
	$(playerController).on("viewCountChanged numWatchingNowChanged playerTypeChanged", function() {
		renderCounts();
	});
	
	if (registerLikeUri) {
		// likes enabled
		$likeButton.click(function() {
			if (!PageData.get("loggedIn")) {
				showLoginRequiredModal();
				return;
			}
			$likeButton.prop("disabled", true);
			// ignoring dislikes for now. could be added in the future
			var type = playerController.getLikeType() !== "like" ? "like" : "reset";
			playerController.registerLike(type, function(success) {
				$likeButton.prop("disabled", false);
			});
		});
	}	
	
	$(playerController).on("likeTypeChanged numLikesChanged streamStateChanged playerTypeChanged", function() {
		renderLikeButton();
	});
	
	$(playerController).on("overrideModeChanged", function() {
		renderOverrideMode();
		renderOverrideButton();
	});
	
	$(playerController).on("scheduledPublishTimeChanged streamStateChanged playerTypeChanged", function() {
		renderBroadcastTime();
	});

	$(playerController).on("playerTypeChanged play ended", function() {
		renderSuggestionSlide();
	});
	
	// time won't appear if in the past,
	// this condition could become true at a point in time
	setInterval(function() {
		renderBroadcastTime();
	}, 3000);
	
	renderCounts();
	renderShareButton();
	renderLikeButton();
	renderBroadcastTime();
	
	
	/* if !responsive then the player should fill the size of the container, minus the space for the nav bar below.
	   The height of the container should not be assumed as constant. E.g. it may be set to fill the document width and height in the case of an iframe */
	function updatePlayerComponentSize() {
		if (responsive) {
			return;
		}
		var containerHeight = $container.innerHeight();
		var bottomContainerHeight = bottomBarMode !== "none" ? $bottomContainer.outerHeight(true) : 0;
		$playerOuter.height(Math.max(containerHeight - bottomContainerHeight, 0));
	}
	
	function renderQualitySelectionComponent() {
		if (qualitySelectionComponent.hasQualities()) {
			$qualitySelectionItemContainer.removeClass("hide");
		}
		else {
			$qualitySelectionItemContainer.addClass("hide");
		}
		updatePlayerComponentSize();
	}
	
	function renderCounts() {
		var viewCount = playerController.getViewCount();
		if (viewCount !== null && viewCount === 0) {
			viewCount = null;
		}
		
		var numWatchingNow = null;
		if (registerWatchingUri) {
			// watching now enabled
			numWatchingNow = playerController.getNumWatchingNow();
		}
		if (numWatchingNow !== null && (playerController.getPlayerType() === "ad" || numWatchingNow === 0)) {
			numWatchingNow = null;
		}
		
		var $els = [[$count1ItemContainer, $count1]];
		if (!embedded && bottomBarMode === "full") {
			// if it's not embedded and bottom bar is in 'full' mode allow 2 rows
			$els.push([$count2ItemContainer, $count2]);
		}
		
		if (viewCount !== null) {
			var $el = $els.shift();
			$el[1].text(viewCount+" view"+(viewCount !== 1 ? "s":""));
			$el[0].removeClass("hide");
		}
		
		if ($els.length > 0) {
			if (numWatchingNow !== null) {
				var $el = $els.shift();
				$el[1].text(numWatchingNow+" watching now");
				$el[0].removeClass("hide");
			}
		}
		
		while($els.length > 0) {
			$el = $els.shift();
			$el[0].addClass("hide");
			$el[1].text("");
		}
		
		updatePlayerComponentSize();
	}
	
	function renderShareButton() {
		if ($playerComponent === null) {
			$shareButtonItemContainer.addClass("hide");
		}
		else {
			// the embedDataAvailable event handler will show it
		}
	}
	
	function renderLikeButton() {
		if (!registerLikeUri) {
			// likes disabled
			return;
		}
		var likeType = playerController.getLikeType();
		var numLikes = playerController.getNumLikes();
		var streamState = playerController.getStreamState();
		var playerType = playerController.getPlayerType();
		var txt = null;
		// ignoring dislikes for now. maybe implement in the future
		
		// enable like button if no content unless it's an ad because stream is over.
		if (playerType === null || (playerType === "ad" && streamState !== 3)) {
			$likeButtonItemContainer.addClass("hide");
		}
		else {
			$likeButtonItemContainer.removeClass("hide");
		}
		
		if (likeType === "like") {
			txt = " Liked!";
		}
		else {
			txt = " Like!";
		}
		if (numLikes !== null) {
			txt = txt+" ("+playerController.getNumLikes()+")";
		}
		$likeButtonTxt.text(txt);
		updatePlayerComponentSize();
	}
	
	function renderOverrideMode() {
		if ($playerComponent === null) {
			return;
		}
		
		if (playerController.getOverrideModeEnabled()) {
			$playerComponent.addClass("override-mode-enabled");
		}
		else {
			$playerComponent.removeClass("override-mode-enabled");
		}
		updatePlayerComponentSize();
	}
	
	function renderOverrideButton() {
		if (!enableAdminOverride) {
			$overrideButtonItemContainer.addClass("hide");
			return;
		}
		
		if (playerController.getOverrideModeEnabled()) {
			$overrideButton.text("Disable Admin Override").removeClass("btn-default").addClass("btn-danger");
		}
		else {
			$overrideButton.text("Enable Admin Override").removeClass("btn-danger").addClass("btn-default");
		}
		updatePlayerComponentSize();
	}
	
	function renderBroadcastTime() {
		if (embedded || bottomBarMode !== "full") {
			// try and prevent 2 rows if embedded, and only show when bottom bar in "full" mode
			return;
		}
		
		var now = SynchronisedTime.getDate();
		var time = playerController.getScheduledPublishTime();
		var streamState = playerController.getStreamState();
		if (time !== null && (playerController.getPlayerType() !== "ad" || streamState === 3) && time.getTime() < now.getTime()) {
			var txt = ""
			if (streamState !== null && streamState >= 3) {
				txt += "Broadcast at ";
			}
			else {
				txt += "Available since ";
			}
			txt += $.format.date(time, "HH:mm on D MMM");
			if (time.getFullYear() !== now.getFullYear()) {
				txt += " "+time.getFullYear();
			}
			if ($broadcastTime.text() !== txt) {
				$broadcastTime.text(txt);
			}
			$broadcastTimeContainer.removeClass("hide");
		}
		else {
			$broadcastTimeContainer.addClass("hide");
			$broadcastTime.text("");
		}
	}

	function renderSuggestionSlide() {
		if (!recommendationsUri) {
			// recommendations disabled
			return;
		}
		if (playerController.getPlayerType() !== "vod" || !playerController.hasEnded()) {
			// slide should not be shown
			if (suggestionSlide) {
				suggestionSlide.getEl().remove();
				suggestionSlide.destroy();
				suggestionSlide = null;
				$playerWrapper.removeClass("invisible");
			}
		}
		else {
			// slide should be shown. player is for VOD and in ended state
			if (!suggestionSlide) {
				var openInNewWindow = embedded;
				suggestionSlide = new PlayerSuggestionSlide(playerController.getCoverUri(), recommendationsUri, onWatchAgainClicked, openInNewWindow);
				$playerOuter.append(suggestionSlide.getEl());
				$playerWrapper.addClass("invisible");
			}
		}

		function onWatchAgainClicked() {
			// seek to 0 and play
			playerController.jumpToTime(0, true);
		}
	}
	
	function showLoginRequiredModal() {
		if (!pleaseLoginModal) {
			pleaseLoginModal = new AlertModal("Account Required", loginRequiredMsg);
		}
		pleaseLoginModal.show(true);
	}
};

module.exports = PlayerContainer;
