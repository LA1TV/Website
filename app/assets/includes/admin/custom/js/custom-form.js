// can group form elements with a certain tag into a virtual form, meaning no <form> tags needed

$(document).ready(function() {
	
	// listen for enter key
	$('input[data-virtualform]').keypress(function(e) {
		
		if (e.which !== 13) {
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
	
	
	$('button[data-virtualformsubmit][data-virtualformsubmitmethod][data-virtualformsubmitaction][data-virtualform]').click(function(e) {
		
		e.preventDefault();
		
		var method = $(this).attr("data-virtualformsubmitmethod");
		var action = $(this).attr("data-virtualformaction");
		var id = $(this).attr("data-virtualform");
		
		// create the form again (off screen) with all the form elements with the same ID and submit it
		var $form = $("<form />").attr("method", method).attr("action", action).addClass("hidden");
		
		$('[data-virtualform="'+id+'"]').each(function() {
			$el = $('<input />').attr("type", "hidden").attr("name", $(this).attr("name")).val($(this).val());
			$form.append($el);
		});
		
		$("body").append($form);
		$form.submit();
		return false;
	});



});