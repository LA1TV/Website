define(["jquery"], function($) {

	var ChapterInput = function(state) {
		
		var self = this;
		
		this.getId = function() {
			return null;
		};
		
		this.getState = function() {
			var time = null;
			if (enteredMinute !== null || enteredSecond !== null) {
				if (enteredMinute === null) {
					enteredMinute = 0;
				}
				if (enteredSecond === null) {
					enteredSecond = 0;
				}
				time = (enteredMinute * 60) + enteredSecond;
			}
			return {
				title: enteredTitle,
				time: time
			};
		};
		
		this.setState = function(state) {
			enteredTitle = state.title;
			if (state.time === null) {
				enteredMinute = null;
				enteredSecond = null;
			}
			else {
				enteredMinute = floor(state.time/60);
				enteredSecond = state.time%60;
			}
			
			$titleEl.val(enteredTitle);
			$minuteEl.val(enteredMinute !== null ? enteredMinute : "");
			$secondEl.val(enteredSecond !== null ? enteredSecond : "");
			$(self).triggerHandler("stateChanged");
		};
		
		this.getEl = function() {
			return $el;
		};
		
		var enteredTitle = null;
		var enteredMinute = null;
		var enteredSecond = null;
		
		var $el = $("<div />").addClass("row");
		var $col = $("<div />").addClass("col-md-8");
		var $titleEl = $("<input />").addClass("form-control").prop("type", "text").attr("placeholder", "Title");
		$col.append($titleEl);
		$el.append($col);
		var $col = $("<div />").addClass("col-md-2");
		var $minuteEl = $("<input />").addClass("form-control").prop("type", "number").attr("min", 0).attr("placeholder", "Minute");
		$col.append($minuteEl);
		$el.append($col);
		var $col = $("<div />").addClass("col-md-2");
		var $secondEl = $("<input />").addClass("form-control").prop("type", "number").attr("min", 0).attr("max", 59).attr("placeholder", "Second");
		$col.append($secondEl);
		$el.append($col);
		$minuteEl.on("keyup change", onChange);
		$secondEl.on("keyup change", onChange);
		$titleEl.on("keyup change", function() {
			var val = $(this).val();
			if (val !== enteredTitle) {
				enteredTitle = val;
				$(self).triggerHandler("stateChanged");
			}
		});
		
		function onChange() {
			var minute = $minuteEl.val();
			if (minute === "") {
				minute = null;
			}
			else {
				minute = parseInt(minute);
				if (isNaN(minute) || minute < 0) {
					$minuteEl.val("");
					alert("The minute was not valid.");
					return;
				}
			}
			var second = $secondEl.val();
			if (second === "") {
				second = null;
			}
			else {
				second = parseInt(second);
				if (isNaN(second) || second < 0 || second >= 60) {
					$secondEl.val("");
					alert("The second was not valid.");
					return;
				}
			}
			
			if (enteredMinute !== minute || enteredSecond !== second) {
				enteredMinute = minute;
				enteredSecond = second;
				$(self).triggerHandler("stateChanged");
			}
		}
		this.setState(state);
	};
	
	return ChapterInput;
});