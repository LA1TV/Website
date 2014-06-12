<?php namespace uk\co\la1tv\website\serviceProviders\csrf;

use Illuminate\Support\Facades\Facade;

class CsrfFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'csrf'; }

}