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
				type: type,
				support: support,
				qualityState: qualityState
			};
		};
		
		this.setState = function(state) {
			url = state.url;
			type = state.type;
			support = state.support;
			qualityState = state.qualityState;
			$urlEl.val(url);
			$typeEl.val(type);
			$supportSelect.val(support);
			qualityAjaxSelect.setState(qualityState);
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $el;
		};
		
		var qualityAjaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/quality-definitions/ajaxselect", {
			id: null,
			text: null
		});
		
		$(qualityAjaxSelect).on("stateChanged", function() {
			qualityState = qualityAjaxSelect.getState();
			$(self).triggerHandler("stateChanged");
		});
		
		var qualityState = null;
		var url = null;
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
		var $typeCol = $("<div />").addClass("col-md-3");
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
		
		$urlEl.on("keyup change", function() {
			var val = $(this).val();
			if (url !== val) {
				url = val;
				$(self).triggerHandler("stateChanged");
			}
		});
	
		$typeEl.on("keyup change", function() {
			var val = $(this).val();
			if (type !== val) {
				type = val;
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