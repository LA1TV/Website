<?php namespace uk\co\la1tv\website\serviceProviders\facebook;

use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('facebook', function()
        {
           return new FacebookManager();
        });
    }

}