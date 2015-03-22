<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use Illuminate\Support\Facades\Facade;

class SmartCacheFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'smartCache'; }

}