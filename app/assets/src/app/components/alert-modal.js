define(["jquery"], function($) {

	var AlertModal = function(heading, msg) {
		
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
		
		var modalTitle = heading;
		var visible = false;
		var animating = false;
		
		var $container = $("<div />").addClass("modal fade").attr("tabindex", "-1").attr("role", "dialog").attr("aria-label", modalTitle).attr("aria-hidden", "true");
		var $modalDialog = $("<div />").addClass("modal-dialog");
		var $modalContent = $("<div />").addClass("modal-content");
		var $modalHeader = $("<div />").addClass("modal-header");
		var $headerCloseBtn = $("<button />").addClass("close").prop("type", "button").html('<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>');
		var $modalTitle = $("<h3 />").addClass("title").text(modalTitle);
		var $modalBody = $("<div />").addClass("modal-body");
		var $modalFooter = $("<div />").addClass("modal-footer");
		var $footerDoneBtn = $("<button />").addClass("btn btn-primary").prop("type", "button").text("OK");
		
		var $msg = $("<div />");
		var lines = msg.split("\n");
		for(var i=0; i<lines.length; i++) {
			var line = lines[i];
			if (line === "") {
				continue;
			}
			$msg.append($("<p />").text(line));
		}
		$modalBody.append($msg);
		
		
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
	
	return AlertModal;
});
