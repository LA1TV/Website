$(document).ready(function() {

	$(".page-playlists-edit").first().each(function() {
	
		var $pageContainer = $(this).first();
	
		$pageContainer.find(".form-playlist-content").each(function() {
			var $container = $(this).first();
			
			var reordableList = new ReordableList(true, true, function(state) {
				return new AjaxSelect("http://127.0.0.1/la1tv/index.php/admin/media/ajaxselect", state);
			}, {
				id: null,
				text: null
			}, []);
			$(reordableList).on("stateChanged", function() {
				console.log(reordableList.getState());
			});
			$container.append(reordableList.getEl());
		});
	});
});