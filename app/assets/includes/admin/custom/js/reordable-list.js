var reordableList = {
	register: null
};

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	reordableList.register = register;
	
	/*
	*  rowElement should be an object with the following functions:
	*  - build(id) return a dom element to be added to the row. Should default to the id passed in. Can be null.
	*  - getId() return an id representing the chosen option
	*  - setId(id) set the id representing the chosen option
	*/
	function register($container, rowElement) {
		
		// contains ListRow's
		var rows = [];
		
		function ListRow(no) {
			var rowNo = null;
		
			var $listRow = $("<div />").addClass("list-row").attr("data-highlight-state", 0);
			var $rowNoCell = $("<div />").addClass("cell cell-no");
			var $contentCell = $("<div />").addClass("cell cell-content");
			var $optionsCell = $("<div />").addClass("cell cell-options");
			var $optionDrag = $("<div />").addClass("option option-drag handle").text("[DRAG]");
			
			$listRow.append($rowNoCell);
			$listRow.append($contentCell);
			$optionsCell.append($optionDrag);
			$listRow.append($optionsCell);
			
			$listRow.hover(function() {
				$listRow.attr("data-highlight-state", 1);
			}, function() {
				$listRow.attr("data-highlight-state", 0);
			});
			
			this.getEl = function() {
				return $listRow;
			};
			
			this.setRowNo = function(no) {
				if (rowNo !== no) {
					rowNo = no;
					$rowNoCell.text((no+1)+".");
					$listRow.attr("data-row", no);
				}
			};
			
			this.setRowNo(no);
			
		}
		
		function createRow(id) {
			var row = new ListRow(rows.length+1);
			rows.push(row);
			updateRowNums();
			$listTable.append(row.getEl());
		}
		
		function updateRowOrder() {
			var newRows = [];
			newRows.length = rows.length;
			for (var i=0; i<rows.length; i++) {
				var row = rows[i];
				var currentRow = $listTable.find(".list-row[data-row]").index(row.getEl());
				newRows[currentRow] = row;
			}
			rows = newRows;
			updateRowNums();
		}
		
		function updateRowNums() {
			for (var i=0; i<rows.length; i++) {
				rows[i].setRowNo(i);
			}
		}
		
		if (!$container.hasClass("reordable-list")) {
			$container.addClass("reordable-list");
		}
		
		$listContainer = $("<div />").addClass("list-container");
		$listTable = $("<div />").addClass("list-table");
		$listContainer.append($listTable);
		
		$listTable.sortable({
			appendTo: $listTable,
			axis: "y",
			containment: $listContainer,
			cursor: "move",
			handle: ".handle",
			items: "> .list-row",
			helper: function(e, $el) {
				return $el.clone().attr("data-highlight-state", 2);
			},
			stop: function() {
				updateRowOrder();
			}
		});
		
		for (var i=0; i<20; i++) {
			createRow(null);
		}
		
		
		$container.append($listContainer);
	};
	
	
	// TODO: TEMPORARY
	register($(".reordable-list").first(), null);
	
});