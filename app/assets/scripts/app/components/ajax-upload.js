define([
	"../page-data",
	"../helpers/file-size-helper",
	"../helpers/ajax-helpers",
	"plupload"
], function(PageData, FileSizeHelper, AjaxHelpers, plupload) {

	var noUploads = 0;

	var AjaxUpload = function(allowedExtensions, uploadPointId, remoteRemove, stateParam) {
		
		var self = this;
		
		this.getId = function() {
			return id;
		};
		
		this.getState = function() {
			return {
				id: id,
				fileName: fileName,
				fileSize: fileSize,
				processState: processState,
				processMsg: processMsg,
				processPercentage: processPercentage
			};
		};
		
		this.setState  = function(stateParam) {
			cancelUpload();
			removeUpload();
			id = stateParam.id;
			fileName = stateParam.fileName;
			fileSize = stateParam.fileSize;
			processState = stateParam.processState;
			processMsg = stateParam.processMsg;
			processPercentage = stateParam.processPercentage;
			if (id !== null) {
				progress = 100;
				state = processState !== 0 ? 2 : 4;
			}
			$(self).triggerHandler("stateChanged");
			update();
		};
		
		this.getEl = function() {
			return $container;
		};
		
		this.destroy = function() {
			destroyed = true;
			cancelUpload();
			removeUpload();
			cancelUpdateProcessStateChecks();
		};
		
		var $container = $("<div />").addClass("ajax-upload");
		
		var $btnContainer = $("<div />").addClass("btn-container");
		
		// the upload button
		var $uploadBtn = $('<button />').prop("type", "button").addClass("btn btn-xs btn-default");
		$btnContainer.append($uploadBtn);
		
		// the upload plugin needs it's own button. giving this one and then clicking it programatically
		var $pluploadButton = $('<button />').prop("type", "button").addClass("hidden");
		
		var $txt = $('<span />').addClass("info-txt");
		$btnContainer.append($txt);
		
		var $progressBarContainer = $("<div />").addClass("progress progress-striped").hide();
		var $progressBar = $("<div />").addClass("progress-bar").attr("role", "progressbar").attr("aria-valuemin", 0).attr("aria-valuemax", 100).attr("aria-valuenow", 0).width("0%");
		$progressBarContainer.append($progressBar);
		
		$container.append($pluploadButton);
		$container.append($btnContainer);
		$container.append($progressBarContainer);
		
		var maxFileLength = 50;
		
		var id = null;
		var uploader = null;
		var fileName = null;
		var fileSize = null;
		var processState = null;
		var processMsg;
		var processPercentage;
		var updateProcessStateTimerId = null;
		var progressBarVisible = false;
		var progressBarActive = false;
		var progressBarPercent = null;
		var currentTxt = null;
		var txtState = null;
		var btnState = null;
		var progress = null;
		var errorMsg = null;
		var destroyed = false;
		var state = 0; // 0=choose file, 1=uploading, 2=uploaded and processed (even if process error), 3=error, 4=uploaded and processing
		var cancelling = false;
		var defaultId = stateParam.id;
		if (defaultId !== null) {
			progress = 100;
			state = stateParam.processState !== 0 ? 2 : 4;
		}
		
		this.setState(stateParam); // calls update()
		updateProcessState(); // starts periodic checks
		
		$uploadBtn.click(function() {
			if (state === 0 || state === 3) {
				// start upload
				$pluploadButton.click();
			}
			else if (state === 1) {
				if (!confirm("Are you sure you want to cancel this upload?")) {
					return;
				}
				cancelUpload();
			}
			else if (state === 2 || state === 4) {
				if (!confirm("Are you sure you want to remove this upload?")) {
					return;
				}
				removeUpload();
				update();
			}
		});
		
		// state: 0=hidden, 1=visible and active, 2=visible
		function updateProgressBar(state, progress) {
			
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
		}
		
		// state: 0=normal, 1=success, 2=error, 3=working
		function updateTxt(state, txt) {
			
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
		
		}
		
		// state: 0=upload, 1=cancel, 2=remove
		function updateBtn(state) {
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
		}
		
		// update the process state
		// this makes an ajax request and updates the processState, processPercentage, and processMsg variables and then calls update
		// this function will automatically be called at set intervals after the first call. However ajax requests will only be made when necessary
		function updateProcessState() {
			
			function startTimer() {
				updateProcessStateTimerId = setTimeout(updateProcessState, 2000);
			}
			
			if (destroyed) {
				return;
			}
			
			if (id === null || processState !== 0) {
				// no file uploaded or the uploaded file has finished processing.
				startTimer();
				return;
			}
			
			var theId = id;
			
			jQuery.ajax(PageData.get("baseUrl")+"/admin/upload/processinfo", {
				cache: false,
				dataType: "json",
				headers: AjaxHelpers.getHeaders(),
				data: {
					id: id,
					csrf_token: PageData.get("csrfToken")
				},
				type: "POST"
			}).done(function(data, textStatus, jqXHR) {
				if (jqXHR.status === 200 && data.success) {
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
		}
		
		function cancelUpdateProcessStateChecks() {
			if (updateProcessStateTimerId !== null) {
				clearTimeout(updateProcessStateTimerId);
			}
		}
		
		// update the dom
		function update() {
			
			var fileStr = "";
			if (state === 1 || state === 2 || state === 4) {
				fileStr = '"'+fileName+'" ('+FileSizeHelper.formatFileSize(fileSize)+')';
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
				else if (processState === 3) { // file waiting to be reprocessed
					str = fileStr+' is waiting to be reprocessed.';
				}
				else {
					console.log("ERROR: Invalid process state.", processState);
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
		}
		
		function errorOccurred() {
			noUploads--;
			if (state === 1) { // this could be an asynchronous callback
				state = 3;
				errorMsg = cancelling ? "Upload cancelled." : "An error occurred. Please try again.";
				update();
			}
			cancelling = false;
		}
		
		
		// Initialize the plupload plugin
		// https://github.com/moxiecode/plupload
		
		uploader = new plupload.Uploader({
			runtimes : 'html5,flash,silverlight,html4',
			browse_button : $pluploadButton[0],
			container: $container[0],
			drop_element: $container[0],
			url: PageData.get("baseUrl")+"/admin/upload/index",
			multi_selection: false,
			flash_swf_url: PageData.get("assetsBaseUrl")+'assets/moxie/Moxie.swf',
			silverlight_xap_url: PageData.get("assetsBaseUrl")+'assets/moxie/Moxie.xap',
			filters: {
				mime_types: [
					{title: allowedExtensions.join(","), extensions: allowedExtensions.join(",")},
				]
			},
			multipart: true,
			multipart_params: {
				upload_point_id: uploadPointId,
				csrf_token: PageData.get("csrfToken"),
				// random number between 0 and 1000000
				id: Math.floor(Math.random() * (1000000 + 1))
			},
			chunk_size: "500mb",
			max_retries: 3,

			init: {
				FilesAdded: function(up, files) {
					if (state !== 0 && state !== 3) {
						// must be in upload state to upload
						// do nothing
						uploader.splice(); // clear queue
						return;
					}
					
					if (files.length !== 1) {
						uploader.splice(); // clear queue
						return;
					}
					
					var file = files[0];
					fileName = file.name;
					fileSize = file.size;
					
					var extension = fileName.split('.').pop().toLowerCase();
					if (jQuery.inArray(extension, allowedExtensions) === -1) {
						alert("That file type is not allowed.");
						uploader.splice(); // clear queue
						return;
					}
					
					if (fileName.length > maxFileLength) {
						alert("The file name must be "+maxFileLength+" characters or less.");
						uploader.splice(); // clear queue
						return;
					}
					
					progress = 0;
					state = 1;
					update();
					
					// start upload automatically
					uploader.start();
					noUploads++;				
				},

				UploadProgress: function(up, file) {
					progress = file.percent;
					update();
				},
				
				FileUploaded: function(up, files, responseData) {
					var status = responseData.status;
					if (status !== 200) {
						errorOccurred();
					}
					else {
						var result = $.parseJSON(responseData.response);
						// response returned is object with 'success' and 'id' which is the id of the newly created file
						if (!result.success) {
							errorOccurred(); // this decrements noUploads
							return;
						}
						
						noUploads--;
						id = result.id;
						fileName = result.fileName;
						fileSize = result.fileSize;
						processState = result.processInfo.state;
						processPercentage = result.processInfo.percentage;
						processMsg = result.processInfo.msg;
						progress = 100;
						state = processState !== 0 ? 2 : 4;
						$(self).triggerHandler("stateChanged");
						update();
					}
				},

				Error: function(up, err) {
					errorOccurred();
				}
			}
		});

		uploader.init();

		
		function cancelUpload() {
			if (state !== 1) {
				return;
			}
			cancelling = true;
			uploader.stop();
			uploader.splice(); // clear queue
			errorOccurred();
		}
		
		function removeUpload() {
			if (state !== 2 && state !== 4) {
				return;
			}
			tmpId = id;
			id = null;
			fileName = null;
			fileSize = null;
			processState = null;
			processPercentage = null;
			processMsg = null;
			doRemoteRemove(tmpId);
			state = 0;
			$(self).triggerHandler("stateChanged");
		}
		
		function doRemoteRemove(id) {
			if (remoteRemove || id !== defaultId) {
				// make ajax request to server to tell it to remove the temporary file immediately
				// don't really care if it fails because the file will be removed when the session ends anyway
				// this will not be made if the user is removing the file that is already saved because remoteRemove should be false and the id should match the one that was there when the page was loaded (because the user could cancel the form and it should still be on the server)
				jQuery.ajax(PageData.get("baseUrl")+"/admin/upload/remove", {
					cache: false,
					dataType: "json",
					headers: AjaxHelpers.getHeaders(),
					data: {
						id: id,
						csrf_token: PageData.get("csrfToken")
					},
					type: "POST"
				});
			}
		}
	};
	
	AjaxUpload.getNoActiveUploads = function() {
		return noUploads;
	};
	
	return AjaxUpload;
});