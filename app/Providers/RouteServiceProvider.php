<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
		 $this->mapNominationRoutes();
		$this->mapIndexCardReportsRoutes();
		$this->mapAffidavitRoutes();
         $this->map_booth_app();
        //
    }
	
	 protected function map_booth_app()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/booth_app.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    protected function mapAffidavitRoutes()
    {
        Route::middleware('web')
             ->namespace('App\Http\Controllers\Affidavit')
             ->group(base_path('routes/affidavit/affidavitweb.php'));
    }   

     protected function mapNominationRoutes()
    {
        Route::prefix('nomination')
        ->middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/nomination.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
	
	/**
     * 
     *
     * Custom Route for Index Card Reports Routes
     *prefix('api')
             ->
     * @return void
     */
    protected function mapIndexCardReportsRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/IndexCardReports/IndexCardReports.php'));
    }
	
	
}
