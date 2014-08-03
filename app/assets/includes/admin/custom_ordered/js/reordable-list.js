var ReordableList = null;

$(document).ready(function() {

	var baseUrl = $("body").attr("data-baseUrl");
	var assetsBaseUrl = $("body").attr("data-assetsbaseurl");
	
	/*
	*  rowElementBuilder should be a function that returns a class with the following functions:
	*  - getEl() return the dom element to be added to the row.
	*  - getId() return an id representing the chosen option
	*  - setState(state) set the the chosen option using a state object
	*  - getState() return the state object for the element
	*
	*  - It will get passed the initial state object as the first parameter
	*/
	ReordableList = function(deleteEnabled, addEnabled, rowElementBuilder, state) {
		
		var self = this;
		
		this.getState = function() {
			var state = [];
			for (var i=0; i<rows.length; i++) {
				var row = rows[i];
				state.push(row.getRowElement().getState());
			}
			return state;
		};
		
		this.setState = function(state) {
			deleteAllRows();
			for(var i=0; i<state.length; i++) {
				var rowState = state[i];
				createRow(rowState);
			}
			render();
		};
		
		this.getEl = function() {
			return $container;
		};
		
		
		var $container = $("<div />").addClass("reordable-list");
		// contains ListRow's
		var rows = [];
		var id = null;
		var $listContainer = $("<div />").addClass("list-container");
		var $listTable = $("<div />").addClass("list-table");
		$listContainer.append($listTable);
		
		this.setState(state); // calls render()
		
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
		
		$container.append($listContainer);
		
		function ListRow(no, rowState) {
			
			var listRowSelf = this;
		
			var rowNo = null;
			var rowElement = rowElementBuilder(rowState);
			var $listRow = $("<div />").addClass("list-row").attr("data-highlight-state", 0);
			var $rowNoCell = $("<div />").addClass("cell cell-no");
			var $contentCell = $("<div />").addClass("cell cell-content");
			var $optionsCell = $("<div />").addClass("cell cell-options");
			if (deleteEnabled) {
				var $optionDelete = $("<div />").addClass("option");
				var $deleteButton = $('<button />').attr("type", "button").addClass("btn btn-xs btn-danger").html("&times;");
			}
			var $optionDrag = $("<div />").addClass("option option-drag handle").text("[DRAG]");
			
			$listRow.append($rowNoCell);
			$listRow.append($contentCell);
			if (deleteEnabled) {
				$optionsCell.append($optionDelete);
				$optionDelete.append($deleteButton);
			}
			$optionsCell.append($optionDrag);
			$contentCell.append(rowElement.getEl());
			$listRow.append($optionsCell);
			
			$deleteButton.click(function() {
				if (confirm("Are you sure you want to delete this row?")) {
					deleteRow(listRowSelf);
				}
			});
			
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
			};
			
			$(rowElement).on("stateChanged", function() {
				$(self).triggerHandler("stateChanged");
			});
			
			this.setRowNo(no);
		}
		
		function render() {
		
		}
		
		function createRow(rowState) {
			var row = new ListRow(rows.length+1, rowState);
			rows.push(row);
			updateRowNums();
			$listTable.append(row.getEl());
		}
		
		function deleteRow(row) {
			deleteRowImpl(row);
			updateRowNums();
			$(self).triggerHandler("stateChanged");
		}
		
		function deleteAllRows() {
			for (var i=0; i<rows.length; i++) {
				deleteRowImpl(rows[i]);
			}
			updateRowNums();
			$(self).triggerHandler("stateChanged");
		}
		
		function deleteRowImpl(row) {
			// TODO: check shift params
			rows.shift(rows.indexOf(row));
			row.getEl().remove();
		}
		
		function updateRowOrder() {
			var orderChanged = false;
			var newRows = [];
			newRows.length = rows.length;
			for (var i=0; i<rows.length; i++) {
				var row = rows[i];
				var currentRowNo = $listTable.find(".list-row[data-row]").index(row.getEl());
				newRows[currentRowNo] = row;
				if (i !== currentRowNo) {
					orderChanged = true;
				}
			}
			if (!orderChanged) {
				return;
			}
			rows = newRows;
			updateRowNums();
			$(self).triggerHandler("stateChanged");
		}
		
		function updateRowNums() {
			for (var i=0; i<rows.length; i++) {
				rows[i].setRowNo(i);
			}
		}
	};
	
});