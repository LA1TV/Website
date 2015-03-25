<?php namespace uk\co\la1tv\website\serviceProviders\apiAuth;

use Illuminate\Support\Facades\Facade;

class ApiAuthFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'apiAuth'; }

}