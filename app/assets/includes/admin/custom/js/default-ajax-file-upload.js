// will attach an AjaxFileUpload to elements with .default-ajax-upload

$(document).ready(function() {

	$(".default-ajax-upload").each(function() {
		var $container = $(this).first();
		// after file uploaded it's id should be stored in hidden form element with name
		var destinationName = $container.attr("data-ajaxuploadresultname");
		// the reference to the hidden form element where the file id should be placed
		var $destinationEl = $container.parent().find('[name="'+destinationName+'"]').first();
		var id = $destinationEl.val();
		id = id === "" ? null : parseInt(id, 10);
		var allowedExtensions = $container.attr("data-ajaxuploadextensions").split(",");
		var uploadPointId = $container.attr("data-ajaxuploaduploadpointid");
		var remoteRemove = $container.attr("data-ajaxuploadremoteremove") === "1";
		var fileName = $container.attr("data-ajaxuploadcurrentfilename");
		var fileSize = parseInt($container.attr("data-ajaxuploadcurrentfilesize"), 10);
		var processState = parseInt($container.attr("data-ajaxuploadprocessstate"), 10);
		var processPercentage = $container.attr("data-ajaxuploadprocesspercentage") !== "" ? parseInt($container.attr("data-ajaxuploadprocesspercentage"), 10) : null;
		var processMsg = $container.attr("data-ajaxuploadprocessmsg") !== "" ? $container.attr("data-ajaxuploadprocessmsg") : null;
		
		var ajaxUpload = new AjaxUpload(allowedExtensions, uploadPointId, remoteRemove, {
			id: id,
			fileName: fileName,
			fileSize: fileSize,
			processState: processState,
			processPercentage: processPercentage,
			processMsg: processMsg
		});
		$(ajaxUpload).on("idChanged", function() {
			$destinationEl.val(ajaxUpload.getId() !== null ? ajaxUpload.getId() : "");
		});
		$container.append(ajaxUpload.getEl());
	});
});