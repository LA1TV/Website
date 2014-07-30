var reordableList = {
	register: null
};

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	reordableList.register = register;
	
	/*
	*  rowElement should be a class with the following functions:
	*  - constructor(state) the state object representing chosen option
	*  - getEl() return the dom element to be added to the row. Should default to the id passed in. Can be null.
	*  - getId() return an id representing the chosen option
	*  - setState(state) set the the chosen option using state object
	*/
	function register($container, RowElement) {
		
		// contains ListRow's
		var rows = [];
		
		function ListRow(no, id) {
			var rowNo = null;
			var rowElement = new RowElement(id);
			var $listRow = $("<div />").addClass("list-row").attr("data-highlight-state", 0);
			var $rowNoCell = $("<div />").addClass("cell cell-no");
			var $contentCell = $("<div />").addClass("cell cell-content");
			var $optionsCell = $("<div />").addClass("cell cell-options");
			var $optionDrag = $("<div />").addClass("option option-drag handle").text("[DRAG]");
			
			$listRow.append($rowNoCell);
			$listRow.append($contentCell);
			$optionsCell.append($optionDrag);
			$contentCell.append(rowElement.getEl());
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
			
			this.getRowElement = function() {
				return rowElement;
			}
			
			this.setRowNo(no);
			
		}
		
		function createRow(id) {
			var row = new ListRow(rows.length+1, id);
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
	register($(".reordable-list").first(), function(id) {
		var $el = $("<div />").text("test");
		var id = -1;
		
		this.getEl = function() {
			return $el;
		};
		
		this.getId = function() {
			return id;
		};
		
		this.setId = function(idParam) {
			id = idParam;
		};
	});
	
});