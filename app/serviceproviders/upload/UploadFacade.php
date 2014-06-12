<?php namespace uk\co\la1tv\website\serviceProviders\upload;

use Illuminate\Support\Facades\Facade;

class UploadFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'upload'; }

}