<?php namespace uk\co\la1tv\website\api;

use Illuminate\Support\ServiceProvider;

class ApiResponseDataGeneratorServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('apiResponseDataGenerator', function()
        {
           return new ApiResponseDataGenerator();
        });
    }

}