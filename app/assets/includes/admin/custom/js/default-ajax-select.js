// will attach an AjaxSelect to elements with .default-ajax-select
$(document).ready(function() {

	$(".default-ajax-select").each(function() {
		var $container = $(this).first();
		var destinationName = $container.attr("data-destinationname");
		// the reference to the hidden form element where chosen rows id should be placed
		var $destinationEl = $container.parent().find('[name="'+destinationName+'"]').first();
	
		var ajaxSelect = new AjaxSelect($container, $destinationEl.val() !== "" ? parseInt($destinationEl.val()) : null);
		$(ajaxSelect).on("idChanged", function() {
			$destinationEl.val(ajaxSelect.getId() !== null ? ajaxSelect.getId() : "");
		});
	});
});