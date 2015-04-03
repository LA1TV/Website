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
				qualityState: qualityState
			};
		};
		
		this.setState = function(state) {
			url = state.url;
			qualityState = state.qualityState;
			$urlEl.val(url);
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
		
		var $el = $("<div />").addClass("row");
		var $qualityCol = $("<div />").addClass("col-md-4");
		var $qualityAjaxSelectEl = qualityAjaxSelect.getEl();
		$qualityCol.append($qualityAjaxSelectEl);
		$el.append($qualityCol);
		var $urlCol = $("<div />").addClass("col-md-8");
		var $urlEl = $("<input />").addClass("form-control").prop("type", "url").attr("placeholder", "Stream URL");
		$urlCol.append($urlEl);
		$el.append($urlCol);
		
		$urlEl.on("keyup change", function() {
			var val = $(this).val();
			if (url !== val) {
				url = val;
				$(self).triggerHandler("stateChanged");
			}
		});

		this.setState(state);
	};
	
	return QualityAndUrlInput;
});