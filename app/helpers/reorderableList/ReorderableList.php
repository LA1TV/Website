<?php namespace uk\co\la1tv\website\helpers\reorderableList;

interface ReorderableList {
	
	// should return true if the $data is valid and all related models exist.
	public function isValid();
	
	// should return the string that should be used as the initial data to populate the orderable list.
	public function getInitialDataString();
	
	// should return the string that should be the initial value in the result input field.
	public function getStringForInput();
}