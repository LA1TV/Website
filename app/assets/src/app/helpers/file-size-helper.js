// Helper function that formats the file sizes

define({
	formatFileSize: function(bytes) {
		if (typeof bytes !== 'number') {
			return '';
		}
		
		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}

		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' MB';
		}

		return (bytes / 1000).toFixed(2) + ' KB';
	}
});