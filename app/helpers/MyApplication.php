<?php

use Illuminate\Foundation\Application;
use Stack\Builder;

class MyApplication extends Application {
	
	// override the part of the framework where it creates the stacked HTTP kernel so that the default session implementation
	// is replaced with a custom one where the session id will be retrieved from an "X-Session-Id" header if can't find it
	// in a cookie
	
	protected function getStackedClient()
	{
		$sessionReject = $this->bound('session.reject') ? $this['session.reject'] : null;

		$client = (new Builder)
                    ->push('Illuminate\Cookie\Guard', $this['encrypter'])
                    ->push('Illuminate\Cookie\Queue', $this['cookie'])
                    ->push('MySessionMiddleware', $this['session'], $sessionReject);

		$this->mergeCustomMiddlewares($client);

		return $client->resolve($this);
	}
	
}