<?php namespace uk\co\la1tv\website\serviceProviders\mySessionMiddleware;

use Illuminate\Support\ServiceProvider;

class MySessionMiddlewareServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
		$manager = $this->app->make("Illuminate\Session\SessionManager");
        $this->app->middleware("MySessionMiddleware", array($manager));
    }

}