<?php namespace uk\co\la1tv\website\serviceProviders\smartCache;

use Illuminate\Support\ServiceProvider;

class SmartCacheServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('smartCache', function()
        {
           return new SmartCacheManager();
        });
    }

}