<?php namespace uk\co\la1tv\website\serviceProviders\csrf;

use Illuminate\Support\ServiceProvider;

class CsrfServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('csrf', function()
        {
           return new CsrfManager();
        });
    }

}