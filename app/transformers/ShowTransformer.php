<?php namespace uk\co\la1tv\website\transformers;

use uk\co\la1tv\website\models\Show;

class ShowTransformer extends Transformer {
	
	public function transform($show) {
		return [
			"id"			=> intval($show->id),
			"name"			=> $show->name,
			"description"	=> $show->description,
			"timeUpdated"	=> $show->updated_at->timestamp
		];
	}
	
}