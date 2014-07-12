<?php namespace uk\co\la1tv\website\serviceProviders\auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('auth', function()
        {
           return new AuthManager();
        });
    }

}