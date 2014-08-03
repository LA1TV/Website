// will attach an AjaxSelect to elements with .default-ajax-select

$(document).ready(function() {

	$(".tmp-reordable-list").each(function() {
		var $container = $(this).first();
		
		var reordableList = new ReordableList([], function() {
			return new AjaxSelect("http://127.0.0.1/la1tv/index.php/admin/media/ajaxselect", {
				id: null,
				chosenText: null
			});
		});
		$(reordableList).on("stateChanged", function() {
			alert("state changed");
		});
		$container.append(reordableList.getEl());
	});
});