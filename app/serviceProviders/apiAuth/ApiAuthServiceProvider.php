<?php namespace uk\co\la1tv\website\serviceProviders\apiAuth;

use Illuminate\Support\ServiceProvider;

class ApiAuthServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('apiAuth', function()
        {
           return new ApiAuthManager();
        });
    }

}