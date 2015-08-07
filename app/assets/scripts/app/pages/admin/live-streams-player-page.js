define([
	"jquery",
	"../../components/player",
	"../../components/quality-selection",
	"../../device-detection",
	"lib/domReady!"
], function($, PlayerComponent, QualitySelectionComponent, DeviceDetection) {
	
	$(".page-livestreams-player").first().each(function() {
	
		var $pageContainer = $(this).first();
		
		$pageContainer.find(".player-container").first().each(function() {
			var coverArtUri = $(this).attr("data-cover-art-uri");
			var streamUriGroups = extractUrisForDevice($.parseJSON($(this).attr("data-stream-uris")));
			var qualities = [];
			for (var i=0; i<streamUriGroups.length; i++) {
				var uriGroup = streamUriGroups[i];
				qualities.push({
					id:		uriGroup.quality.id,
					name:	uriGroup.quality.name
				});
			}
			
			if (streamUriGroups.length === 0) {
				var $alert = $("<div />").addClass("alert alert-info").prop("role", "alert").append($("<span />").addClass("glyphicon glyphicon-info-sign")).append($("<span />").text(" There are no stream urls available."));
				$(this).append($alert);
			}
			else {
				var qualitySelectionComponent = new QualitySelectionComponent();
				qualitySelectionComponent.setAvailableQualities(qualities, false);
				
				var playerComponent = new PlayerComponent(coverArtUri, true, null);
				playerComponent.setPlayerType("live");
				updatePlayerUris();
				playerComponent.showPlayer(true);
				playerComponent.render();
				
				$(qualitySelectionComponent).on("chosenQualityChanged", function() {
					updatePlayerUris();
					playerComponent.render();
				});
				
				var $row = $("<div />").addClass("row");
				var $col = $("<div />").addClass("col-md-6 col-md-offset-3");
				$row.append($col);
				$col.append(playerComponent.getEl());
				var $bottomRow = $("<div />").addClass("clearfix bottom-row");
				var $qualitySelectionContainer = $("<div />").addClass("quality-selection-container");
				$bottomRow.append($qualitySelectionContainer);
				$qualitySelectionContainer.append(qualitySelectionComponent.getEl());
				$col.append($bottomRow);
				$(this).append($row);
			}
			
			
			// returns an array of uri groups with any uris that aren't supported on this device stripped out.
			// any uri groups that become empty because of this will be stripped out as well.
			function extractUrisForDevice(uriGroups) {
				var deviceUriGroups = [];
				for (var i=0; i<uriGroups.length; i++) {
					var uriGroup = uriGroups[i];
					var newUriGroup = {
							uris: [],
							quality: uriGroup.quality
					};
					for (var j=0; j<uriGroup.uris.length; j++) {
						var uri = uriGroup.uris[j];
						var supportedDevices = uri.supportedDevices;
						// if supportedDevices is null then that means all devices are supported. Otherwise only the devices listed are supported.
						if (supportedDevices !== null) {
							supportedDevices = supportedDevices.split(",");
						}
						var currentDevice = DeviceDetection.isMobile() ? "mobile" : "desktop";
						if (supportedDevices !== null && $.inArray(currentDevice, supportedDevices) === -1) {
							// uri not supported on this device
							continue;
						}
						newUriGroup.uris.push(uri);
					}
					if (newUriGroup.uris.length > 0) {
						deviceUriGroups.push(newUriGroup);
					}
				}
				return deviceUriGroups;
			}
			
			// returns the uri group that should be used for the chosen quality
			function getChosenUris() {
				var uris = [];
				var qualityIds = [];
				for (var i=0; i<streamUriGroups.length; i++) {
					var uriGroup = streamUriGroups[i];
					qualityIds.push(uriGroup.quality.id);
				}
				if (qualityIds.length > 0) {
					var currentQualityId = qualitySelectionComponent.getChosenQualityId();
					var chosenUriGroup = streamUriGroups[qualityIds.indexOf(currentQualityId)];
					uris = chosenUriGroup.uris;
				}
				return uris;
			}
			
			function updatePlayerUris() {
				playerComponent.setPlayerUris(getChosenUris());
				playerComponent.setPlayerStartTime(0, true);
			}
			
		});
		
	});
	
});