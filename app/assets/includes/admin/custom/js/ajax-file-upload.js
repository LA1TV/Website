// handles attaching jquery.fileupload to all .ajax-upload

// will be set to function to return number of active uploads.
var getNoUploads = null;


$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var noUploads = 0;
	
	getNoUploads = function() {
		return noUploads;
	};
	
	
	$(".ajax-upload").each(function() {
	
		var self = this;
	
		// after file uploaded it's id should be stored in hidden form element with name
		var name = $(this).attr("data-ajaxuploadresultname");
		
		// the reference to the hidden form element where the file id should be placed
		var $idInput = $(this).parent().find('[name="'+name+'"]').first();
		
		// the generated <input type="file">. Note: this gets replaced by jquery file upload after every time files are selected
		// therefore always search for this element, don't use this reference as it will only be correct initially
		var $fileInput = $('<input />').prop("type", "file").addClass("hidden");
		
		// use this to get input for reason above
		var getFileInput = function() {
			return $(self).find('input[type="file"]').first();
		};
		
		var $btnContainer = $("<div />").addClass("btn-container");
		
		// the upload button
		var $uploadBtn = $('<button />').prop("type", "button").addClass("btn btn-xs btn-default");
		$btnContainer.append($uploadBtn);
		
		var $txt = $('<span />').addClass("info-txt");
		$btnContainer.append($txt);
		
		var $progressBarContainer = $("<div />").addClass("progress progress-striped").hide();
		var $progressBar = $("<div />").addClass("progress-bar").attr("role", "progressbar").attr("aria-valuemin", 0).attr("aria-valuemax", 100).attr("aria-valuenow", 0).width("0%");
		$progressBarContainer.append($progressBar);
		
		$(this).append($fileInput);
		$(this).append($btnContainer);
		$(this).append($progressBarContainer);
		
		var jqXHR = null;
		var fileName = null;
		var fileSize = null;
		var progressBarVisible = false;
		var progressBarActive = false;
		var progressBarPercent = null;
		var currentTxt = null;
		var txtState = null;
		var btnState = null;
		var progress = null;
		var errorMsg = null;
		var state = 0; // 0=choose file, 1=uploading, 2=uploaded, 3=error
		var cancelling = false;
		
		// state: 0=hidden, 1=visible and active, 2=visible
		var updateProgressBar = function(state, progress) {
			
			if (state === 1 || state === 2) { // bar is visible
				if (progressBarPercent !== progress) {
					$progressBar.width(progress+"%");
					$progressBar.attr("aria-valuenow", progress);
					progressBarPercent = progress;
				}
			}
			
			if (state === 0) { //hidden
				if (progressBarVisible) {
					$progressBarContainer.hide();
					progressBarVisible = false;
				}
				if (progressBarActive) {
					$progressBarContainer.removeClass("active");
					progressBarActive = false;
				}
			}
			else if (state === 1) { // visible and active
				if (!progressBarActive) {
					$progressBarContainer.addClass("active");
					progressBarActive = true;
				}
				if (!progressBarVisible) {
					$progressBarContainer.show();
					progressBarVisible = true;
				}
			}
			else if (state === 2) {
				if (progressBarActive) {
					$progressBarContainer.removeClass("active");
					progressBarActive = false;
				}
				if (!progressBarVisible) {
					$progressBarContainer.show();
					progressBarVisible = true;
				}
			}
		};
		
		// state: 0=normal, 1=success, 2=error
		var updateTxt = function(state, txt) {
			
			if (currentTxt !== txt) {
				$txt.text(txt);
				currentTxt = txt;
			}
			
			if (txtState !== state) {
				$txt.removeClass("text-success text-danger");
				if (state === 0) { //normal
					// intentional
				}
				else if (state === 1) { //success
					$txt.addClass("text-success");
				}
				else if (state === 2) { //error
					$txt.addClass("text-danger");
				}
				txtState = state;
			}
		
		};
		
		// state: 0=upload, 1=cancel, 2=remove
		var updateBtn = function(state) {
			if (btnState === state) {
				return;
			}
			
			$uploadBtn.blur();
			$uploadBtn.removeClass("btn-default btn-info btn-danger");
			if (state === 0) {
				$uploadBtn.text("Upload");
				$uploadBtn.addClass("btn-info");
			}
			else if (state === 1) {
				$uploadBtn.text("Cancel");
				$uploadBtn.addClass("btn-danger");
			}
			else if (state === 2) {
				$uploadBtn.text("Remove");
				$uploadBtn.addClass("btn-default");
			}
		};
		
		// update the dom
		var update = function() {
			var str = "";
			if (state === 0 || state === 3) { // choose file
				if (state === 0) {
					updateTxt(0, "Choose file.");
				}
				else if (state === 3) {
					updateTxt(2, errorMsg);
				}
				updateBtn(0);
				updateProgressBar(0);
			}
			else if (state === 1) { // uploading
				updateTxt(0, 'Uploading "'+fileName+'" ('+formatFileSize(fileSize)+'). '+progress+"%");
				updateBtn(1);
				updateProgressBar(1, progress);
			}
			else if (state === 2) { // uploaded
				updateTxt(1, '"'+fileName+'" ('+fileSize+') uploaded!');
				updateBtn(2);
				updateProgressBar(2, progress);
			}
		};
		
		update();
		
		var errorOccurred = function() {
			noUploads--;
			state = 3;
			errorMsg = cancelling ? "Upload cancelled." : "An error occurred. Please try again.";
			cancelling = false;
			update();
		};
		
		// Initialize the jQuery File Upload plugin
		$fileInput.fileupload({
			dropZone: $(self),
			pasteZone: $(self),
			url: baseUrl+"/admin/upload",
			dataType: "json",
			type: "POST",
			limitConcurrentUploads: 3,
			multipart: true,
			formData: function() {
				// don't send any extra data.
				return [];
			},
			// This function is called when a file is added to the queue;
			// either via the browse button, or via drag/drop:
			add: function(e, data) {
			
				if (state !== 0 && state !== 3) {
					// must be in upload state to upload
					return;
				}
			
				fileName = data.files[0].name;
				fileSize = data.files[0].size;
				progress = 0;
				state = 1;
				update();
				
				// start upload automatically
				jqXHR = data.submit();
				noUploads++;
			},
			progress: function(e, data) {
				// Calculate the completion percentage of the upload
				progress = parseInt(data.loaded / data.total * 100, 10);
				update();
			},
			fail: function(e, data) {
				errorOccurred();
			},
			done: function(e, data) {
				var result = data.result;
				// response returned is object with 'success' and 'id' which is the id of the newly created file
				if (!result.success) {
					errorOccurred();
					return;
				}
				// place the file id in the hidden element
				$idInput.val(result.id);
				noUploads--;
				state = 2
				update();
			}
		});
		
		
		$uploadBtn.click(function() {
			
			if (state === 0 || state === 3) {
				// start upload
				var input = getFileInput();
				input.click();
			}
			else if (state === 1) {
				if (!confirm("Are you sure you want to cancel this upload?")) {
					return;
				}
				// cancel upload
				cancelling = true;
				jqXHR.abort(); // this triggers the error callback
			}
			else if (state === 2) {
				if (!confirm("Are you sure you want to remove this upload?")) {
					return;
				}
				// clear current upload
				$idInput.val("");
				state = 0;
				update();
			}
		});
	});
	
});