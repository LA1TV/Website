var reordableList = {
	register: null
};

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	reordableList.register = register;
	
	function register($container) {
		
		if (!$container.hasClass("reordable-list")) {
			$container.addClass("reordable-list");
		}
		
		var $listContainer = $container.find(".list-container").first();
		
		/* $container.droppable({
			accept: function(el) {
				// TODO: might be wrong way around
				return jQuery.inArray($container.find(".list-row"), el) !== -1;
			},
			
		});
		*/
		
		$listContainer.sortable({
			
			appendTo: $listContainer,
			axis: "y",
			containment: $listContainer,
			cursor: "move",
			handle: ".handle",
			items: "> .list-row"
		});
		
		
	};
	
	// TEMPORARY
	register($(".reordable-list").first());
	
});