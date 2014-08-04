// will attach an AjaxSelect to elements with .default-ajax-select

$(document).ready(function() {

	$(".tmp-reordable-list").each(function() {
		var $container = $(this).first();
		
		var reordableList = new ReordableList(true, true, function(state) {
			return new AjaxSelect("http://127.0.0.1/la1tv/index.php/admin/media/ajaxselect", state);
		}, {
			id: null,
			text: null
		}, [
			{
				id: null,
				text: null
			},
			{
				id: 5,
				text: "Sally is annoying."
			}
		]);
		$(reordableList).on("stateChanged", function() {
			console.log(reordableList.getState());
		});
		$container.append(reordableList.getEl());
	});
});