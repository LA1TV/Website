define([
	"jquery",
	"./quality-selection",
	"./share-modal",
	"../player-controller",
	"../page-data",
	"lib/jquery.hotkeys",
	"lib/jquery.dateFormat",
	"lib/domReady!"
], function($, QualitySelectionComponent, ShareModal, PlayerController, PageData) {
	
	var PlayerContainer = function(playerInfoUri, registerViewCountUri, registerWatchingUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay) {

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
		
		var $container = $("<div />").addClass("player-container");
		if (embedded) {
			$container.addClass("embedded-player-container");
		}
		var $bottomContainer = $("<div />").addClass("bottom-container clearfix");
		var $count1ItemContainer = $("<div />").addClass("item-container");
		var $count1 = $("<div />").addClass("view-count").hide();
		$count1ItemContainer.append($count1);
		var $count2ItemContainer = $("<div />").addClass("item-container new-line");
		var $count2 = $("<div />").addClass("view-count").hide();
		$count2ItemContainer.append($count2);
		var $likeButtonItemContainer = $("<div />").addClass("item-container right");
		var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
		var $likeButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-thumbs-up");
		var $likeButtonTxt = $("<span />");
		$likeButtonItemContainer.append($likeButton);
		var $shareButtonItemContainer = $("<div />").addClass("item-container right");
		var $shareButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
		var $shareButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-share");
		var $shareButtonTxt = $("<span />").text(" Share");
		$shareButtonItemContainer.append($shareButton);
		var $overrideButtonItemContainer = $("<div />").addClass("item-container");
		var $overrideButton = $("<button />").attr("type", "button").addClass("override-button btn btn-default btn-xs");
		$overrideButtonItemContainer.append($overrideButton);
		var $broadcastTimeContainer = $("<div />").addClass("item-container right new-line");
		var $broadcastTime = $("<div />").addClass("broadcast-on-msg").hide();
		$broadcastTimeContainer.append($broadcastTime);
		var $playerComponent = null;
		$likeButton.append($likeButtonGlyphicon);
		$likeButton.append($likeButtonTxt);
		$shareButton.append($shareButtonGlyphicon);
		$shareButton.append($shareButtonTxt);
		var $qualitySelectionItemContainer = $("<div />").addClass("item-container right");
		
		$bottomContainer.append($count1ItemContainer);
		$bottomContainer.append($overrideButtonItemContainer);
		$bottomContainer.append($qualitySelectionItemContainer);
		$bottomContainer.append($likeButtonItemContainer);
		$bottomContainer.append($shareButtonItemContainer);
		$bottomContainer.append($count2ItemContainer);
		$bottomContainer.append($broadcastTimeContainer);
		
		var loaded = false;
		var responsive = !embedded;
		
		var qualitySelectionComponent = new QualitySelectionComponent();
		if (!placeQualitySelectionComponentInPlayer) {
			// quality selection component should be added to bottom row.
			$(qualitySelectionComponent).on("qualitiesChanged", function() {
				renderQualitySelectionComponent();
			});
			renderQualitySelectionComponent();
		}
		$qualitySelectionItemContainer.append(qualitySelectionComponent.getEl());
		
		
		var playerController = new PlayerController(playerInfoUri, registerViewCountUri, registerWatchingUri, registerLikeUri, updatePlaybackTimeBaseUri, qualitySelectionComponent, responsive, autoPlayVod, autoPlayStream, vodPlayStartTime, ignoreExternalStreamUrl, initialVodQualityId, initialStreamQualityId, disableFullScreen, placeQualitySelectionComponentInPlayer, showTitleInPlayer, disablePlayerControls, enableSmartAutoPlay);
		$(playerController).on("playerComponentElAvailable", function() {
			$playerComponent = playerController.getPlayerComponentEl();
			$container.append($playerComponent);
			if (!hideBottomBar) {
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
		});
		
		$(playerController).on("viewCountChanged numWatchingNowChanged playerTypeChanged", function() {
			renderCounts();
		});
		
		$likeButton.click(function() {
			if (!PageData.get("loggedIn")) {
				alert(loginRequiredMsg);
				return;
			}
			$likeButton.prop("disabled", true);
			// ignoring dislikes for now. could be added in the future
			var type = playerController.getLikeType() !== "like" ? "like" : "reset";
			playerController.registerLike(type, function(success) {
				$likeButton.prop("disabled", false);
			});
		});
		
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
			if (responsive || $playerComponent === null) {
				return;
			}
			var containerHeight = $container.innerHeight();
			var bottomContainerHeight = !hideBottomBar ? $bottomContainer.outerHeight(true) : 0;
			var playerComponentPadding = $playerComponent.outerHeight(true) - $playerComponent.height();
			$playerComponent.height(Math.max(containerHeight - bottomContainerHeight - playerComponentPadding, 0));
		}
		
		function renderQualitySelectionComponent() {
			if (qualitySelectionComponent.hasQualities()) {
				$qualitySelectionItemContainer.css("display", "inline-block");
			}
			else {
				$qualitySelectionItemContainer.css("display", "none");
			}
			updatePlayerComponentSize();
		}
		
		function renderCounts() {
			var viewCount = playerController.getViewCount();
			if (viewCount !== null && viewCount === 0) {
				viewCount = null;
			}
			
			var numWatchingNow = playerController.getNumWatchingNow();
			if (numWatchingNow !== null && (playerController.getPlayerType() === "ad" || numWatchingNow === 0)) {
				numWatchingNow = null;
			}
			
			var $els = [$count1];
			if (!embedded) {
				// if it's not embedded allow 2 rows
				$els.push($count2);
			}
			
			if (viewCount !== null) {
				$els.shift().text(viewCount+" view"+(viewCount !== 1 ? "s":"")).show()
			}
			
			if ($els.length > 0) {
				if (numWatchingNow !== null) {
					$els.shift().text(numWatchingNow+" watching now").show();
				}
			}
			
			while($els.length > 0) {
				$els.shift().text("").hide();
			}
			
			updatePlayerComponentSize();
		}
		
		function renderShareButton() {
			if ($playerComponent === null) {
				$shareButtonItemContainer.css("display", "none");
			}
			else {
				$shareButtonItemContainer.css("display", "inline-block");
			}
		}
		
		function renderLikeButton() {
			var likeType = playerController.getLikeType();
			var numLikes = playerController.getNumLikes();
			var streamState = playerController.getStreamState();
			var playerType = playerController.getPlayerType();
			var txt = null;
			// ignoring dislikes for now. maybe implement in the future
			
			// enable like button if no content unless it's an ad because stream is over.
			if (playerType === null || (playerType === "ad" && streamState !== 3)) {
				$likeButtonItemContainer.css("display", "none");
			}
			else {
				$likeButtonItemContainer.css("display", "inline-block");
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
				$overrideButton.css("display", "none");
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
			if (embedded) {
				// try and prevent 2 rows if embedded
				return;
			}
			
			var now = new Date();
			var time = playerController.getScheduledPublishTime();
			if (time !== null && playerController.getPlayerType() !== "ad" && time.getTime() < now.getTime()) {
				var txt = ""
				var streamState = playerController.getStreamState();
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
				$broadcastTime.show();
			}
			else {
				$broadcastTime.hide().text("");
			}
		}
	};
	return PlayerContainer;
});