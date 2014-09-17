<?php namespace uk\co\la1tv\website\controllers\embed;

use View;

class EmbedController extends EmbedBaseController {

	public function getIndex() {
		$this->setContent(View::make("embed.player"), "player", "LA1:TV- [PROGRAMME TILE]");
	}
}
