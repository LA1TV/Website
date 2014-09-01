<?php namespace uk\co\la1tv\website\serviceProviders\facebook;

use Illuminate\Support\Facades\Facade;

class FacebookFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'facebook'; }

}