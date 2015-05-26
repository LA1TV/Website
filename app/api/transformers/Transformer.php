<?php namespace uk\co\la1tv\website\api\transformers;

abstract class Transformer {

	// the $options array will be passed to every item
	public function transformCollection(array $items, array $options=[]) {
		if (count($items) === 0) {
			return [];
		}
		return array_map([$this, "transform"], $items, array_fill(0, count($items), $options));
	}
	
	public abstract function transform($item, array $options);
	
}