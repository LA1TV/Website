define(["jquery", "lib/bootstrap"], function($) {

	var CookieComplainceModal = function() {
		
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
		
		var animating = false;
		var visible = false;
		var modalTitle = "Use of Cookies";
		
		var $container = $("<div />").addClass("cookie-compliance-modal modal fade").attr("tabindex", "-1").attr("role", "dialog").attr("aria-label", modalTitle).attr("aria-hidden", "true");
		var $modalDialog = $("<div />").addClass("modal-dialog");
		var $modalContent = $("<div />").addClass("modal-content");
		var $modalHeader = $("<div />").addClass("modal-header");
		var $infoGlyph = $('<span />').addClass("glyphicon glyphicon-info-sign").attr("aria-hidden", true);
		var $modalTitle = $("<h3 />").addClass("title");
		$modalTitle.append($infoGlyph);
		$modalTitle.append($("<span />").text(" "+modalTitle));
		var $modalBody = $("<div />").addClass("modal-body");
		var $modalFooter = $("<div />").addClass("modal-footer");
		var $footerOKBtn = $("<button />").addClass("btn btn-primary").prop("type", "button").text("OK");
		
		$modalBody.append("<p />").text("We use cookies to ensure that we give you the best experience on our website. If you do not like the use of cookies please disable them in your web browser.");
		
		
		$modalDialog.append($modalContent);
		$modalContent.append($modalHeader);
		$modalHeader.append($modalTitle);
		$modalContent.append($modalBody);
		$modalContent.append($modalFooter);
		$modalFooter.append($footerOKBtn);
		$container.append($modalDialog);
		
		$footerOKBtn.click(hide);
		
		var $body = $("body").first();

		$container.modal({
			backdrop: 'static',
			keyboard: false,
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
	
	return CookieComplainceModal;
});
