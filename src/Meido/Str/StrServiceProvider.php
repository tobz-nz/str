<?php namespace Meido\Str;

use Illuminate\Support\ServiceProvider;
use Meido\Str\Str;

class StrServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['str'] = $this->app->share(function($app)
		{
			return new Str;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('str');
	}

}