define([
	"jquery",
	"../page-data",
	"./ajax-select"
], function($, PageData, AjaxSelect) {

	var QualityAndUrlInput = function(state) {
		
		var self = this;
		
		this.getId = function() {
			return null;
		};
		
		this.getState = function() {
			return {
				url: url,
				dvrBridgeServiceUrl: dvrBridgeServiceUrl,
				thumbnailsUrl: thumbnailsUrl,
				nativeDvr: nativeDvr,
				type: type,
				support: support,
				qualityState: qualityState
			};
		};
		
		this.setState = function(state) {
			url = state.url;
			dvrBridgeServiceUrl = state.dvrBridgeServiceUrl;
			thumbnailsUrl = state.thumbnailsUrl;
			nativeDvr = state.nativeDvr;
			type = state.type;
			support = state.support;
			qualityState = state.qualityState;
			$urlEl.val(url);
			$thumbnailsUrlEl.val(thumbnailsUrl || "");
			$dvrBridgeCheckbox.prop("checked", dvrBridgeServiceUrl);
			$nativeDvrCheckbox.prop("checked", nativeDvr);
			$typeEl.val(type);
			$supportSelect.val(support);
			qualityAjaxSelect.setState(qualityState);
			onDvrBridgeCheckboxChanged(true);
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $el;
		};
		
		var qualityAjaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/qualitydefinitions/ajaxselect", {
			id: null,
			text: null
		});
		
		$(qualityAjaxSelect).on("stateChanged", function() {
			qualityState = qualityAjaxSelect.getState();
			$(self).triggerHandler("stateChanged");
		});
		
		var qualityState = null;
		var url = null;
		var dvrBridgeServiceUrl = false;
		var thumbnailsUrl = null;
		var nativeDvr = false;
		var type = null;
		var support = null;
		
		var $el = $("<div />").addClass("row");
		var $qualityCol = $("<div />").addClass("col-md-2");
		var $qualityAjaxSelectEl = qualityAjaxSelect.getEl();
		$qualityCol.append($qualityAjaxSelectEl);
		$el.append($qualityCol);
		var $urlCol = $("<div />").addClass("col-md-4");
		var $urlEl = $("<input />").addClass("form-control").prop("type", "url").attr("placeholder", "Stream URL");
		var $thumbnailsUrlEl = $("<input />").addClass("form-control").prop("type", "url").attr("placeholder", "Stream URL For Thumbnails (Optional)");
		$urlCol.append($("<div />").append($urlEl));
		$urlCol.append($("<div />").append($thumbnailsUrlEl));
		$el.append($urlCol);
		var $dvrBridgeCol = $("<div />").addClass("col-md-1");
		var $dvrBridgeCheckbox = $("<input />").prop("type", "checkbox");
		$dvrBridgeCol.append($("<div />").addClass("checkbox").append($("<label />").append($dvrBridgeCheckbox).append($("<span />").text("URL for DVR bridge service?"))));
		$el.append($dvrBridgeCol);
		var $nativeDvrCol = $("<div />").addClass("col-md-1");
		var $nativeDvrCheckbox = $("<input />").prop("type", "checkbox");
		var $nativeDvrCheckboxContainer = $("<div />").addClass("checkbox");
		$nativeDvrCheckboxContainer.append($("<label />").append($nativeDvrCheckbox).append($("<span />").text("URL has native DVR support?")));
		$nativeDvrCol.append($nativeDvrCheckboxContainer);
		$el.append($nativeDvrCol);
		var $typeCol = $("<div />").addClass("col-md-2");
		var $typeEl = $("<input />").addClass("form-control").prop("type", "text").attr("placeholder", "Stream Type");
		$typeCol.append($typeEl);
		$el.append($typeCol);
		var $supportCol = $("<div />").addClass("col-md-2");
		var $supportSelect = $("<select />").addClass("form-control");
		$supportSelect.append($("<option />").text("PC + Mobile").val("all"));
		$supportSelect.append($("<option />").text("PC Only").val("pc"));
		$supportSelect.append($("<option />").text("Mobile Only").val("mobile"));
		$supportSelect.append($("<option />").text("Disabled").val("none"));
		$supportCol.append($supportSelect);
		$el.append($supportCol);
		
		var onTypeChanged = function() {
			var val = $typeEl.val();
			if (type !== val) {
				type = val;
				$(self).triggerHandler("stateChanged");
			}
		};
		$typeEl.on("keyup change", onTypeChanged);
		
		var onDvrBridgeCheckboxChanged = function(initial) {
			var checked = $dvrBridgeCheckbox.prop("checked");
			if (checked) {
				$typeEl.prop("readonly", true).val("application/x-mpegURL");
				$nativeDvrCheckbox.prop("disabled", true).prop("checked", false);
				nativeDvr = null;
				$nativeDvrCheckboxContainer.addClass("disabled");
			}
			else {
				$typeEl.prop("readonly", false);
				if (initial !== true) {
					$typeEl.val("");
					$nativeDvrCheckbox.prop("disabled", false).prop("checked", false);
					nativeDvr = false;
					$nativeDvrCheckboxContainer.removeClass("disabled");
				}
			}
			onTypeChanged();
			if (dvrBridgeServiceUrl !== checked) {
				dvrBridgeServiceUrl = checked;
				$(self).triggerHandler("stateChanged");
			}
		};
		$dvrBridgeCheckbox.change(onDvrBridgeCheckboxChanged);
		$nativeDvrCheckbox.change(function() {
			nativeDvr = $nativeDvrCheckbox.prop("checked");
			$(self).triggerHandler("stateChanged");
		});
		
		$urlEl.on("keyup change", function() {
			var val = $(this).val();
			if (url !== val) {
				url = val;
				$(self).triggerHandler("stateChanged");
			}
		});

		$thumbnailsUrlEl.on("keyup change", function() {
			var val = $(this).val();
			if (val === "") {
				val = null;
			}
			if (thumbnailsUrl !== val) {
				thumbnailsUrl = val;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		$supportSelect.on("change", function() {
			var val = $(this).val();
			if (support !== val) {
				support = val;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		this.setState(state);
	};
	
	return QualityAndUrlInput;
});