<?php namespace uk\co\la1tv\website\serviceProviders\upload;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
    {
        $this->app->bind('upload', function()
        {
           return new UploadManager();
        });
    }

}