// will attach an AjaxFileUpload to elements with .default-ajax-upload

define(["jquery", "./components/ajax-upload", "lib/domReady!"], function($, AjaxUpload) {

	$(".default-ajax-upload").each(function() {
		var $container = $(this).first();
		var options = AjaxUpload.getOptionsFromDom($container);
		var id = options.id;
		var allowedExtensions = options.allowedExtensions;
		var uploadPointId = options.uploadPointId;
		var fileName = options.fileName;
		var fileSize = options.fileSize;
		var processState = options.processState;
		var processPercentage = options.processPercentage;
		var processMsg = options.processMsg;
		
		// after file uploaded it's id should be stored in hidden form element with name
		var destinationName = $container.attr("data-ajaxuploadresultname");
		// the reference to the hidden form element where the file id should be placed
		var $destinationEl = $container.parent().find('[name="'+destinationName+'"]').first();
		var ajaxUpload = new AjaxUpload(allowedExtensions, uploadPointId, {
			id: id,
			fileName: fileName,
			fileSize: fileSize,
			processState: processState,
			processPercentage: processPercentage,
			processMsg: processMsg
		});
		$(ajaxUpload).on("stateChanged", function() {
			$destinationEl.val(ajaxUpload.getId() !== null ? ajaxUpload.getId() : "");
		});
		$container.append(ajaxUpload.getEl());
	});
	
});