// can group form elements with a certain tag into a virtual form, meaning no <form> tags needed

define([
	"jquery",
	"./page-protect",
	"./helpers/submit-request",
	"lib/domReady!"
], function($, PageProtect, submitVirtualForm) {

	var CustomForm = {
		handlers: {},
		addHandler: function(id, handler) {
			this.handlers[id] = handler;
			return handler;
		},
		removeHandler: function(id, handler) {
			delete this.handlers[id];
		}
	};

	// listen for enter key
	$('[data-virtualform]').keyup(function(e) {
		
		if (e.which !== 13) {
			return;
		}
	
		if ($(this).prop("tagName").toLowerCase() === "textarea") {
			return;
		}
	
		var attr = $(this).attr("data-virtualformsubmit");
		if (typeof attr !== 'undefined' && attr !== false) {
			return;
		}
		
		var id = parseInt($(this).attr("data-virtualform"));
		
		e.preventDefault();
		
		$('button[data-virtualform="'+id+'"][data-virtualformsubmit]').first().click();
		return false;
	});
	
	
	$('button[data-virtualformsubmit][data-virtualformsubmitmethod][data-virtualformsubmitaction][data-virtualform]').each(function() {
		
		var attr = $(this).attr("data-virtualformconfirm");
		if (typeof attr !== 'undefined' && attr !== false) {
			PageProtect.enable(attr === "" ? "Are you sure you want to leave this page without saving?" : attr);
		}
		
		$(this).click(function(e) {
			
			e.preventDefault();
			
			for (var key in CustomForm.handlers) {
				if (!CustomForm.handlers[key]()) {
					return false;
				}
			}
			
			var method = $(this).attr("data-virtualformsubmitmethod");
			var action = $(this).attr("data-virtualformaction");
			var id = $(this).attr("data-virtualform");
			
			var data = {};
			data["form-submitted"] = ""+id;
			
			$('[data-virtualform="'+id+'"]').each(function() {
				
				var attr = $(this).attr("name");
				if (typeof attr === 'undefined' || attr === false) {
					return true; // continue
				}
				
				var disabledAttr = $(this).attr("disabled");
				if (typeof disabledAttr !== 'undefined' && disabledAttr !== false) {
					return true; // continue
				}
				
				if ($(this).prop("type").toLowerCase() === "checkbox" || $(this).prop("type").toLowerCase() === "radio") {
					data[attr] = $(this).prop("checked") ? $(this).val() : "";
				}
				else {
					data[attr] = $(this).val();
				}
			});
			
			PageProtect.disable();
			
			submitVirtualForm("post", "", data);
			return false;
		});
	});
	
	return CustomForm;
});