// handles attaching jquery.fileupload to all .ajax-upload

// will be set to function to return number of active uploads.
var getNoActiveUploads = null;


$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var noUploads = 0;
	
	getNoActiveUploads = function() {
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
		
		var allowedExtensions = $(this).attr("data-ajaxuploadextensions").split(",");
		var uploadPointId = $(this).attr("data-ajaxuploaduploadpointid");
		var remoteRemove = $(this).attr("data-ajaxuploadremoteremove") === "1";
		var maxFileLength = 50;
		
		var jqXHR = null;
		var id = $idInput.val();
		id = id === "" ? null : parseInt(id, 10);
		var fileName = null;
		var fileSize = null;
		var processState = null;
		var processPercentage = null;
		var processMsg = null;
		var progressBarVisible = false;
		var progressBarActive = false;
		var progressBarPercent = null;
		var currentTxt = null;
		var txtState = null;
		var btnState = null;
		var progress = null;
		var errorMsg = null;
		var state = 0; // 0=choose file, 1=uploading, 2=uploaded and processed (even if process error), 3=error, 4=uploaded and processing
		var cancelling = false;
		var defaultId = id;
		
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
		
		// state: 0=normal, 1=success, 2=error, 3=working
		var updateTxt = function(state, txt) {
			
			if (currentTxt !== txt) {
				$txt.text(txt);
				currentTxt = txt;
			}
			
			if (txtState !== state) {
				$txt.removeClass("text-success text-danger text-info");
				if (state === 0) { //normal
					// intentional
				}
				else if (state === 1) { //success
					$txt.addClass("text-success");
				}
				else if (state === 2) { //error
					$txt.addClass("text-danger");
				}
				else if (state === 3) { //working
					$txt.addClass("text-info");
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
		
		// update the process state
		// this makes an ajax request and updates the processState, processPercentage, and processMsg variables and then calls update
		// this function will automatically be called at set intervals after the first call. However ajax requests will only be made when necessary
		var updateProcessState = function() {
			
			var startTimer = function() {
				setTimeout(updateProcessState, 2000);
			};
			
			if (id === null || processState !== 0) {
				// no file uploaded or the uploaded file has finished processing.
				startTimer();
				return;
			}
			
			// the order that the js files are loaded is indeterminable so the file containing this function might not have been loaded yet.
			// if this is the case just ignore and try again later. This could be improved by using something like requireJS in the future to manage dependencies.
			if (typeof(getCsrfToken) !== "function") {
				startTimer();
				return;
			}
			
			var theId = id;
			
			jQuery.ajax(baseUrl+"/admin/upload/processinfo", {
				cache: false,
				dataType: "json",
				data: {
					id: id,
					csrf_token: getCsrfToken()
				},
				type: "POST"
			}).done(function(data) {
				if (data.success) {
					var processInfo = data.payload;
					processState = processInfo.state;
					processPercentage = processInfo.percentage;
					processMsg = processInfo.msg;
					
					if (processState !== 0 && state === 4 && id === theId) {
						// if finished processing and state hasn't been changed somewhere else, and it's still the same file
						state = 2; // uploaded and processed (or error processing)
					}
					update();
				}
			}).always(function() {
				// call this function again in 2 seconds
				startTimer();
			});
		};
		
		// update the dom
		var update = function() {
			
			var fileStr = "";
			if (state === 1 || state === 2 || state === 4) {
				fileStr = '"'+fileName+'" ('+formatFileSize(fileSize)+')';
			}
		
			if (state === 0 || state === 3) { // choose file
				if (state === 0) {
					updateTxt(0, "Choose file.");
				}
				else if (state === 3 || state === 4) {
					updateTxt(2, errorMsg);
				}
				updateBtn(0);
				updateProgressBar(0);
			}
			else if (state === 1) { // uploading
				updateTxt(0, 'Uploading '+fileStr+': '+progress+"%");
				updateBtn(1);
				updateProgressBar(1, progress);
			}
			else if (state === 2) { // uploaded and processed
				var str = null;
				if (processState === 1) { // processed successfully
					str = fileStr+' uploaded and processed!';
				}
				else if (processState === 2) { // error processing
					if (processMsg !== null) {
						str = 'Error processing '+fileStr+': '+processMsg;
					}
					else {
						str = 'Error processing '+fileStr+'.';
					}
				}
				else {
					console.log("ERROR: Invalid process state.");
					return;
				}
				updateTxt(processState === 1 ? 1 : 2, str);
				updateBtn(2);
				updateProgressBar(2, progress);
			}
			else if (state === 4) { // uploaded and processing
				var str = null;
				if (processMsg !== null || processPercentage !== null) {
					str = fileStr+' processing:';
					if (processMsg !== null) {
						str += " "+processMsg;
					}
					if (processPercentage !== null) {
						str += " "+processPercentage+"% complete.";
					}
				}
				else {
					str =  fileStr+' processing...';
				}
				updateTxt(3, str);
				updateBtn(2);
				updateProgressBar(1, progress);
			}
		};
		
		if (defaultId !== null) {
			// populate with default values
			fileName = $(this).attr("data-ajaxuploadcurrentfilename");
			fileSize = parseInt($(this).attr("data-ajaxuploadcurrentfilesize"), 10);
			processState = parseInt($(this).attr("data-ajaxuploadprocessstate"), 10);
			processPercentage = $(this).attr("data-ajaxuploadprocesspercentage") !== "" ? parseInt($(this).attr("data-ajaxuploadprocesspercentage"), 10) : null;
			processMsg = $(this).attr("data-ajaxuploadprocessmsg") !== "" ? $(this).attr("data-ajaxuploadprocessmsg") : null;
			progress = 100;
			state = processState !== 0 ? 2 : 4;
		}
		
		updateProcessState(); // starts periodic checks
		update();
		
		var errorOccurred = function() {
			noUploads--;
			state = 3;
			errorMsg = cancelling ? "Upload cancelled." : "An error occurred. Please try again.";
			cancelling = false;
			update();
		};
		
		// Initialize the jQuery File Upload plugin
		// https://github.com/blueimp/jQuery-File-Upload/
		$fileInput.fileupload({
			dropZone: $(self),
			pasteZone: $(self),
			url: baseUrl+"/admin/upload/index",
			dataType: "json",
			type: "POST",
			limitConcurrentUploads: 3,
			multipart: true,
			// extra data to be sent
			formData: function() {
				return [
					{name: 'upload_point_id', value: uploadPointId},
					{name: 'csrf_token', value: getCsrfToken()}
				]
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
				
				var extension = fileName.split('.').pop().toLowerCase();
				if (jQuery.inArray(extension, allowedExtensions) === -1) {
					alert("That file type is not allowed.");
					return;
				}
				
				if (fileName.length > maxFileLength) {
					alert("The file name must be "+maxFileLength+" characters or less.");
					return;
				}
				
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
				noUploads--;
				
				id = result.id;
				fileName = result.fileName;
				fileSize = result.fileSize;
				processState = result.processInfo.state;
				processPercentage = result.processInfo.percentage;
				processMsg = result.processInfo.msg;
				
				// place the file id in the hidden element
				$idInput.val(result.id);
				
				$(self).attr("data-ajaxuploadcurrentfilename", fileName);
				$(self).attr("data-ajaxuploadcurrentfilesize", fileSize);
				$(self).attr("data-ajaxuploadprocessstate", processState);
				$(self).attr("data-ajaxuploadprocesspercentage", processPercentage);
				$(self).attr("data-ajaxuploadprocessMsg", processMsg);
				progress = 100;
				state = processState !== 0 ? 2 : 4;
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
			else if (state === 2 || state === 4) {
				if (!confirm("Are you sure you want to remove this upload?")) {
					return;
				}
				// clear current upload
				tmpId = id;
				$idInput.val("");
				id = null;
				fileName = null;
				fileSize = null;
				processState = null;
				processPercentage = null;
				processMsg = null;
				$(self).attr("data-ajaxuploadcurrentfilename", "");
				$(self).attr("data-ajaxuploadcurrentfilesize", "");
				$(self).attr("data-ajaxuploadprocessstate", "");
				$(self).attr("data-ajaxuploadprocesspercentage", "");
				$(self).attr("data-ajaxuploadprocessMsg", "");
				if (remoteRemove || tmpId !== defaultId) {
					// make ajax request to server to tell it to remove the temporary file immediately
					// don't really care if it fails because the file will be removed when the session ends anyway
					// this will not be made if the user is removing the file that is already saved because remoteRemove should be false and the id should match the one that was there when the page was loaded (because the user could cancel the form and it should still be on the server)
					jQuery.ajax(baseUrl+"/admin/upload/remove", {
						cache: false,
						dataType: "json",
						data: {
							id: tmpId,
							csrf_token: getCsrfToken()
						},
						type: "POST"
					});
				}
				state = 0;
				update();
			}
		});
	});
	
});