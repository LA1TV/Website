define([
	"../page-data",
	"../helpers/file-size-helper",
	"../helpers/ajax-helpers",
	"plupload"
], function(PageData, FileSizeHelper, AjaxHelpers, plupload) {

	var numActiveUploads = 0;

	var AjaxUpload = function(allowedExtensions, uploadPointId, stateParam, removeEnabled) {
		
		var self = this;
		removeEnabled = typeof(removeEnabled) !== "undefined" ? removeEnabled : true;

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

		// get the id of the file that is currently stored on the server
		this.getRemoteId = function() {
			return remoteId;
		};

		// set the id of the file that is currently stored on the server
		this.setRemoteId = function(id) {
			remoteId = id;
		};
		
		this.getEl = function() {
			return $container;
		};

		this.removeUpload = function() {
			removeUpload();
			update();
		};

		this.cancelUpload = function() {
			cancelUpload();
			update();
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
		var $uploadBtn = $('<button />').prop("type", "button").addClass("btn btn-xs btn-default upload-btn");
		$btnContainer.append($uploadBtn);

		// the download button
		var $downloadBtn = $('<button />').prop("type", "button").addClass("btn btn-xs btn-info download-btn").text("Download");
		$btnContainer.append($downloadBtn);
		
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
		var remoteId = stateParam.id;
		if (remoteId !== null) {
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

		$downloadBtn.click(function() {
			if (state !== 2) {
				throw "Invalid state.";
			}
			else if (processState !== 1) {
				throw "Processing must have succeeeded.";
			}
			window.open(getDownloadLink());
		});

		function getDownloadLink() {
			if (state !== 2) {
				throw "Invalid state.";
			}
			else if (processState !== 1) {
				throw "Processing must have succeeeded.";
			}
			// everything after the ID is optional and discarded on the server
			// it's there to try and make sure that when it is downloaded it gets the nice file name
			return PageData.get("baseUrl")+"/admin/upload/"+encodeURIComponent(id)+"/"+encodeURIComponent(fileName);
		}
		
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
		function updateUploadBtn(state) {
			if (btnState === state) {
				return;
			}
			
			$uploadBtn.blur();
			$uploadBtn.removeClass("btn-default btn-info btn-danger hidden");
			if (state === 0) {
				$uploadBtn.text("Upload");
				$uploadBtn.addClass("btn-info");
			}
			else if (state === 1) {
				$uploadBtn.text("Cancel");
				$uploadBtn.addClass("btn-danger");
			}
			else if (state === 2) {
				if (removeEnabled) {
					$uploadBtn.text("Remove");
					$uploadBtn.addClass("btn-default");
				}
				else {
					$uploadBtn.addClass("hidden");
				}
			}
		}

		// state: 0=hide download button, 1=show download button
		function updateDownloadButton(state) {
			if (state === 0) {
				$downloadBtn.addClass("hidden").prop("disabled", true);
			}
			else if (state === 1) {
				$downloadBtn.removeClass("hidden").prop("disabled", false);
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
				updateUploadBtn(0);
				updateDownloadButton(0);
				updateProgressBar(0);
			}
			else if (state === 1) { // uploading
				updateTxt(0, 'Uploading '+fileStr+': '+progress+"%");
				updateUploadBtn(1);
				updateDownloadButton(0);
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
				updateUploadBtn(2);
				updateDownloadButton(processState === 1 ? 1 : 0);
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
				updateUploadBtn(2);
				updateDownloadButton(0);
				updateProgressBar(1, progress);
			}
		}
		
		function errorOccurred() {
			numActiveUploads--;
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
					numActiveUploads++;				
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
							errorOccurred(); // this decrements numActiveUploads
							return;
						}
						
						numActiveUploads--;
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
			if (id !== remoteId) {
				// make ajax request to server to tell it to remove the temporary file immediately
				// don't really care if it fails because the file will be removed when the session ends anyway
				// this will not be made if the user is removing the file that is already saved because the id should match the one that was there when the page was loaded (because the user could cancel the form and it should still be on the server)
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
		return numActiveUploads;
	};

	AjaxUpload.getOptionsFromDom = function($el) {
		var id = $el.attr("data-ajaxuploadfileid");
		return {
			id: id !== "" ? parseInt(id, 10) : null,
			allowedExtensions: $el.attr("data-ajaxuploadextensions").split(","),
			uploadPointId: parseInt($el.attr("data-ajaxuploaduploadpointid"), 10),
			fileName: $el.attr("data-ajaxuploadcurrentfilename"),
			fileSize: $el.attr("data-ajaxuploadcurrentfilesize") !== "" ? parseInt($el.attr("data-ajaxuploadcurrentfilesize"), 10) : null,
			processState: parseInt($el.attr("data-ajaxuploadprocessstate"), 10),
			processPercentage: $el.attr("data-ajaxuploadprocesspercentage") !== "" ? parseInt($el.attr("data-ajaxuploadprocesspercentage"), 10) : null,
			processMsg: $el.attr("data-ajaxuploadprocessmsg") !== "" ? $el.attr("data-ajaxuploadprocessmsg") : null,
		};
	};
	return AjaxUpload;
});