// will attach an AjaxSelect to elements with .default-ajax-select

$(document).ready(function() {

	$(".tmp-reordable-list").each(function() {
		var $container = $(this).first();
		
		var reordableList = new ReordableList(true, true, function(state) {
		//	return new AjaxSelect("http://127.0.0.1/la1tv/index.php/admin/media/ajaxselect", state);
			
			return new AjaxUpload(["jpg"], 1, true, state);
		}, {
			id: null,
			fileName: null,
			fileSize: null,
			processState: null,
			processPercentage: null,
			processMsg: null
		}, []);
		$(reordableList).on("stateChanged", function() {
			console.log(reordableList.getState());
		});
		$container.append(reordableList.getEl());
	});
});