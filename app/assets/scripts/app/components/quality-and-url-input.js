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
				dvr: dvr,
				type: type,
				support: support,
				qualityState: qualityState
			};
		};
		
		this.setState = function(state) {
			url = state.url;
			dvr = state.dvr;
			type = state.type;
			support = state.support;
			qualityState = state.qualityState;
			$urlEl.val(url);
			$dvrCheckbox.prop("checked", dvr);
			$typeEl.val(type);
			$supportSelect.val(support);
			qualityAjaxSelect.setState(qualityState);
			onCheckboxChanged(true);
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
		var dvr = false;
		var type = null;
		var support = null;
		
		var $el = $("<div />").addClass("row");
		var $qualityCol = $("<div />").addClass("col-md-2");
		var $qualityAjaxSelectEl = qualityAjaxSelect.getEl();
		$qualityCol.append($qualityAjaxSelectEl);
		$el.append($qualityCol);
		var $urlCol = $("<div />").addClass("col-md-5");
		var $urlEl = $("<input />").addClass("form-control").prop("type", "url").attr("placeholder", "Stream URL");
		$urlCol.append($urlEl);
		$el.append($urlCol);
		var $dvrCol = $("<div />").addClass("col-md-1");
		var $dvrCheckbox = $("<input />").prop("type", "checkbox");
		$dvrCol.append($("<div />").addClass("checkbox").append($("<label />").append($dvrCheckbox).append($("<span />").text("DVR"))));
		$el.append($dvrCol);
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
		
		var onCheckboxChanged = function(initial) {
			var checked = $dvrCheckbox.prop("checked");
			if (checked) {
				$typeEl.prop("readonly", true).val("application/x-mpegURL");
			}
			else {
				$typeEl.prop("readonly", false);
				if (initial !== true) {
					$typeEl.val("");
				}
			}
			onTypeChanged();
			if (dvr !== checked) {
				dvr = checked;
				$(self).triggerHandler("stateChanged");
			}
		};
		$dvrCheckbox.change(onCheckboxChanged);
		
		$urlEl.on("keyup change", function() {
			var val = $(this).val();
			if (url !== val) {
				url = val;
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