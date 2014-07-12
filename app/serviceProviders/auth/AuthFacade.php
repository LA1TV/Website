<?php namespace uk\co\la1tv\website\serviceProviders\auth;

use Illuminate\Support\Facades\Facade;

class AuthFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth'; }

}