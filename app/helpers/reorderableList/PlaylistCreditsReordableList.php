<?php namespace uk\co\la1tv\website\helpers\reorderableList;

use uk\co\la1tv\website\models\PlaylistProductionRole;

class PlaylistCreditsReorderableList extends CreditsReorderableList {

	protected function getProductionRoleModel() {
		return new PlaylistProductionRole();
	}
	
}