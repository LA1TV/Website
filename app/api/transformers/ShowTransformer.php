<?php namespace uk\co\la1tv\website\api\transformers;

use uk\co\la1tv\website\models\Show;

class ShowTransformer extends Transformer {
	
	public function transform($show, array $options) {
		return [
			"id"			=> intval($show->id),
			"name"			=> $show->name,
			"description"	=> $show->description,
			"siteUrl"		=> $show->getUri(),
			"timeUpdated"	=> $show->updated_at->timestamp
		];
	}
	
}