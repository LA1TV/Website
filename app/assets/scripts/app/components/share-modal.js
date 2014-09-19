define(["jquery", "lib/bootstrap"], function($) {

	// embedData should be an object containing: embedCodeTemplate, facebookShareUri, twitterShareUri
	var ShareModal = function(embedData) {
		
		var self = this;
		
		this.show = function(showParam) {
			showParam ? show() : hide();
		};
		
		this.getVisible = function() {
			return visible;
		};
		
		this.isAnimating = function() {
			return animating;
		};
		
		this.destroy = function() {
			$container.remove();
		};
		
		var embeddedPlayerWidth = 600;
		var embeddedPlayerHeight = 400;
		
		var modalTitle = "Share!";
		var visible = false;
		var animating = false;
		var embedCode = embedData.embedCodeTemplate.replace("{w}", embeddedPlayerWidth).replace("{h}", embeddedPlayerHeight);
		
		var $container = $("<div />").addClass("share-modal modal fade").attr("tabindex", "-1").attr("role", "dialog").attr("aria-label", modalTitle).attr("aria-hidden", "true");
		var $modalDialog = $("<div />").addClass("modal-dialog");
		var $modalContent = $("<div />").addClass("modal-content");
		var $modalHeader = $("<div />").addClass("modal-header");
		var $headerCloseBtn = $("<button />").addClass("close").prop("type", "button").html('<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>');
		var $modalTitle = $("<h3 />").addClass("title").text(modalTitle);
		var $modalBody = $("<div />").addClass("modal-body");
		var $modalFooter = $("<div />").addClass("modal-footer");
		var $footerDoneBtn = $("<button />").addClass("btn btn-primary").prop("type", "button").text("Done");

		var $modalBodyEls = {
			$facebookHeading: $("<h4 />").addClass("heading").text("Facebook"),
			$facebookShareButton: $("<button />").addClass("btn btn-info btn-block").text("Click To Share On Facebook"),
			$twitterHeading: $("<h4 />").addClass("heading").text("Twitter"),
			$twitterShareButton: $("<button />").addClass("btn btn-info btn-block").text("Click To Share On Twitter"),
			$embedHeading: $("<h4 />").addClass("heading").text("Embed Code"),
			$embedContainer: $("<div />").addClass("embed-container"),
			$embedInstruction: $("<span />").addClass("instruction").text("Copy and paste the code below onto your webpage."),
			$embedInput: $("<input />").addClass("embed-code-input form-control").prop("type", "text").prop("readonly", true).val(embedCode)
		};
		
		$modalBodyEls.$facebookShareButton.click(function() {
			window.open(embedData.facebookShareUri, "_blank", "location=no, menubar=no, toolbar=no");
		});
		
		$modalBodyEls.$twitterShareButton.click(function() {
			window.open(embedData.twitterShareUri, "_blank", "location=no, menubar=no, toolbar=no");
		});
		
		$modalBody.append($modalBodyEls.$facebookHeading);
		$modalBody.append($modalBodyEls.$facebookShareButton);
		$modalBody.append($modalBodyEls.$twitterHeading);
		$modalBody.append($modalBodyEls.$twitterShareButton);
		$modalBody.append($modalBodyEls.$embedHeading);
		$modalBodyEls.$embedContainer.append($modalBodyEls.$embedInstruction);
		$modalBodyEls.$embedContainer.append($modalBodyEls.$embedInput);
		$modalBody.append($modalBodyEls.$embedContainer);
		
		
		$modalDialog.append($modalContent);
		$modalContent.append($modalHeader);
		$modalHeader.append($headerCloseBtn);
		$modalHeader.append($modalTitle);
		$modalContent.append($modalBody);
		$modalContent.append($modalFooter);
		$modalFooter.append($footerDoneBtn);
		$container.append($modalDialog);
		
		$headerCloseBtn.click(hide);
		$footerDoneBtn.click(hide);
		
		var $body = $("body").first();

		$container.modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		$body.append($container);
		
		$container.on("show.bs.modal hide.bs.model", function() {
			animating = true;
		});
		
		$container.on("shown.bs.modal", function() {
			animating = false;
			visible = true;
			$(self).triggerHandler("finishedAnimation");
			$(self).triggerHandler("visible");
		});
		
		$container.on("hidden.bs.modal", function() {
			animating = false;
			visible = false;
			$(self).triggerHandler("finishedAnimation");
			$(self).triggerHandler("hidden");
		});
		
		function show() {
			if (visible || animating) {
				return;
			}
			animating = true;
			$container.modal("show");
		}
		
		function hide() {
			if (!visible || animating) {
				return;
			}
			animating = true;
			$container.modal("hide");
		}
	};
	
	return ShareModal;
});
