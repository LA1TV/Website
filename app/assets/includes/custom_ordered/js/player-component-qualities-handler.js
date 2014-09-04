var PlayerComponentQualitiesHandler = null;

$(document).ready(function() {
	
	
	PlayerComponentQualitiesHandler = function(supportedQualities) {
		
		var self = this;
		
		this.getChosenQuality = function() {
			return supportedQualities[0];
		};
		
	};
});