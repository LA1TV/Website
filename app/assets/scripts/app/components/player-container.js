define([
	"jquery",
	"./quality-selection",
	"./share-modal",
	"../player-controller",
	"../page-data",
	"lib/domReady!"
], function($, QualitySelectionComponent, ShareModal, PlayerController, PageData) {
	
	var PlayerContainer = function(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeBaseUri, enableAdminOverride, loginRequiredMsg, embedded, autoPlay, vodPlayStartTime, ignoreExternalStreamUrl, hideBottomBar) {

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
		var $viewCount = $("<div />").addClass("view-count").css("display", "none");
		var $rightSection = $("<div />").addClass("right-section");
		var $likeButtonItemContainer = $("<div />").addClass("item-container");
		var $likeButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
		var $likeButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-thumbs-up");
		var $likeButtonTxt = $("<span />");
		var $shareButtonItemContainer = $("<div />").addClass("item-container");
		var $shareButton = $("<button />").attr("type", "button").addClass("btn btn-default btn-xs");
		var $shareButtonGlyphicon = $("<span />").addClass("glyphicon glyphicon-share");
		var $shareButtonTxt = $("<span />").text(" Share");
		var $overrideButton = $("<button />").attr("type", "button").addClass("override-button btn btn-default btn-xs");
		var $playerComponent = null;
		$likeButton.append($likeButtonGlyphicon);
		$likeButton.append($likeButtonTxt);
		$shareButton.append($shareButtonGlyphicon);
		$shareButton.append($shareButtonTxt);
		var $qualitySelectionItemContainer = $("<div />").addClass("item-container");
		
		$bottomContainer.append($viewCount);
		$bottomContainer.append($overrideButton);
		$bottomContainer.append($rightSection);
		$rightSection.append($shareButtonItemContainer);
		$shareButtonItemContainer.append($shareButton);
		$rightSection.append($likeButtonItemContainer);
		$likeButtonItemContainer.append($likeButton);
		$rightSection.append($qualitySelectionItemContainer);
		
		var loaded = false;
		var responsive = !embedded;
		
		var qualitySelectionComponent = new QualitySelectionComponent();
		$(qualitySelectionComponent).on("qualitiesChanged", function() {
			renderQualitySelectionComponent();
		});
		renderQualitySelectionComponent();
		$qualitySelectionItemContainer.append(qualitySelectionComponent.getEl());
		
		
		var playerController = new PlayerController(playerInfoUri, registerViewCountUri, registerLikeUri, updatePlaybackTimeBaseUri, qualitySelectionComponent, responsive, autoPlay, vodPlayStartTime, ignoreExternalStreamUrl);
		$(playerController).on("playerComponentElAvailable", function() {
			$playerComponent = playerController.getPlayerComponentEl();
			$container.append($playerComponent);
			if (!hideBottomBar) {
				$container.append($bottomContainer);
			}
			renderOverrideMode();
			renderOverrideButton();
			renderShareButton();
			$overrideButton.click(function() {
				playerController.enableOverrideMode(!playerController.getOverrideModeEnabled());
			});
			updatePlayerComponentSize();
			loaded = true;
			$(self).triggerHandler("loaded");
		});
		
		$(playerController).on("embedDataAvailable", function() {
			var shareModal = new ShareModal(playerController.getEmbedData());
			$shareButton.click(function() {
				if (playerController.getPlayerType() === "vod") {
					// if the user is watching a video pause it. if it's a live stream leave it going.
					playerController.pause();
				}
				shareModal.show(true);
			});
		});
		
		$(playerController).on("viewCountChanged playerTypeChanged", function() {
			renderViewCount();
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
		
		renderViewCount();
		renderShareButton();
		renderLikeButton();
		
		
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
		
		function renderViewCount() {
			var viewCount = playerController.getViewCount();
			if (viewCount !== null && (playerController.getPlayerType() !== "ad" || viewCount > 0)) {
				$viewCount.text(viewCount+" view"+(viewCount !== 1 ? "s":"")).css("display", "inline-block");
			}
			else {
				$viewCount.text("").css("display", "none");
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
	};
	return PlayerContainer;
});