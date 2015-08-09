define([
	"jquery",
	"../page-data",
	"./ajax-select"
], function($, PageData, AjaxSelect) {

	var CreditInput = function(state) {
		
		var self = this;
		
		this.getId = function() {
			return null;
		};
		
		this.getState = function() {
			return {
				productionRoleState: productionRoleState,
				siteUserState: siteUserState,
				nameOverride: nameOverride
			};
		};
		
		this.setState = function(state) {
			productionRoleState = state.productionRoleState;
			siteUserState = state.siteUserState;
			nameOverride = state.nameOverride;
			$nameOverrideEl.val(nameOverride !== null ? nameOverride : "");
			$nameOverrideCheckbox.prop("checked", nameOverride !== null);
			productionRoleAjaxSelect.setState(productionRoleState);
			siteUserAjaxSelect.setState(siteUserState !== null ? siteUserState : {id: null, text: null});
			renderSiteUserCol(nameOverride !== null);
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $el;
		};
		
		var productionRoleAjaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/productionroles/ajaxselect", {
			id: null,
			text: null
		}, "Select role...");
		
		$(productionRoleAjaxSelect).on("stateChanged", function() {
			productionRoleState = productionRoleAjaxSelect.getState();
			$(self).triggerHandler("stateChanged");
		});
		
		var siteUserAjaxSelect = new AjaxSelect(PageData.get("baseUrl")+"/admin/siteusers/ajaxselect", {
			id: null,
			text: null
		}, "Select person...");
		
		$(siteUserAjaxSelect).on("stateChanged", function() {
			siteUserState = siteUserAjaxSelect.getState();
			$(self).triggerHandler("stateChanged");
		});
		
		var productionRoleState = null;
		var siteUserState = null;
		var nameOverride = null;
	
		var $el = $("<div />").addClass("row");
		var $productionRoleCol = $("<div />").addClass("col-md-5");
		var $productionRoleAjaxSelectEl = productionRoleAjaxSelect.getEl();
		$productionRoleCol.append($productionRoleAjaxSelectEl);
		$el.append($productionRoleCol);
		
		var $siteUserCol = $("<div />").addClass("col-md-5");
		var $siteUserAjaxSelectEl = siteUserAjaxSelect.getEl();
		$siteUserCol.append($siteUserAjaxSelectEl);
		var $nameOverrideEl = $("<input />").addClass("form-control").prop("type", "text").attr("placeholder", "Overridden name...");
		$siteUserCol.append($nameOverrideEl);
		$el.append($siteUserCol);
		
		var $nameOverrideCheckboxCol = $("<div />").addClass("col-md-2");
		var $nameOverrideCheckbox = $("<input />").prop("type", "checkbox");
		$nameOverrideCheckboxCol.append($("<div />").addClass("checkbox").append($("<label />").append($nameOverrideCheckbox).append($("<span />").text("Override Name"))));
		$el.append($nameOverrideCheckboxCol);
		
		var renderSiteUserCol = function(checked) {
			if (checked) {
				$siteUserAjaxSelectEl.hide();
				$nameOverrideEl.show();
			}
			else {
				$nameOverrideEl.hide();
				$siteUserAjaxSelectEl.show();
			}
		};
		
		var onCheckboxChanged = function() {
			var checked = $nameOverrideCheckbox.prop("checked");
			
			if ((nameOverride !== null) !== checked) {
				if (checked) {
					if (!confirm("Are you sure you want to provide the name manually?\n\nIf the person already exists in the list they should be chosen from there instead, or if they don't they should log into the website first, and then their name will appear here.\n\nIf they don't appear in the initial list you may need to start entering their name to filter the results.")) {
						$nameOverrideCheckbox.prop("checked", false);
						return;
					}
				}
				renderSiteUserCol(checked);
				nameOverride = checked ? "" : null;
				siteUserState = !checked ? {
					id: null,
					text: null
				} : null;
				if (checked) {	
					$nameOverrideEl.val("");
				}
				else {
					siteUserAjaxSelect.setState({id: null, text: null});
				}
				$(self).triggerHandler("stateChanged");
			}
		};
		$nameOverrideCheckbox.change(onCheckboxChanged);
		
		$nameOverrideEl.on("keyup change", function() {
			var val = $(this).val();
			if (nameOverride !== val) {
				nameOverride = val;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		this.setState(state);
	};
	
	return CreditInput;
});