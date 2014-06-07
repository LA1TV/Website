// can group form elements with a certain tag into a virtual form, meaning no <form> tags needed

var customForm = {
	handlers: {},
	addHandler: function(id, handler) {
		this.handlers[id] = handler;
		return handler;
	},
	removeHandler: function(id, handler) {
		delete this.handlers[id];
	}
};

$(document).ready(function() {
	
	// listen for enter key
	$('[data-virtualform]').keypress(function(e) {
		
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
		
		e.preventDefault();
		
		$('button[data-virtualform][data-virtualformsubmit]').click();
		return false;
	});
	
	
	$('button[data-virtualformsubmit][data-virtualformsubmitmethod][data-virtualformsubmitaction][data-virtualform]').each(function() {
		
		var attr = $(this).attr("data-virtualformconfirm");
		if (typeof attr !== 'undefined' && attr !== false) {
			pageProtect.enable(attr === "" ? "Are you sure you want to leave this page without saving?" : attr);
		}
		
		$(this).click(function(e) {
			
			e.preventDefault();
			
			for (var key in customForm.handlers) {
				if (!customForm.handlers[key]()) {
					return false;
				}
			}
			
			var method = $(this).attr("data-virtualformsubmitmethod");
			var action = $(this).attr("data-virtualformaction");
			var id = $(this).attr("data-virtualform");
			
			// create the form again (off screen) with all the form elements with the same ID and submit it
			var $form = $("<form />").attr("method", method).attr("action", action).addClass("hidden");
			
			var data = {};
			data["form-submitted"] = 1;
			
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
			
			for (var key in data) {
				$el = $('<input />').attr("type", "hidden").attr("name", key).val(data[key]);
				$form.append($el);
			}
			
			$("body").append($form);
			
			pageProtect.disable();
			
			$form.submit();
			return false;
		});
	});


});