<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
		
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
			\App\Http\Middleware\SwitchDatabase::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\DataMiddleware::class,
			\App\Http\Middleware\Urlredirect_Middleware::class,
            
        ],

        'api' => [
           // 'throttle:60,1',
            'bindings',
			\App\Http\Middleware\SetDB_API_Middleware::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        //'admin' => \App\Http\Middleware\CheckAdmin::class,
        
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        
        'eci' => \App\Http\Middleware\EciAuthenticated::class,
		'eci_agent' => \App\Http\Middleware\EciAgentMiddleware::class,
        'deo' => \App\Http\Middleware\DeoAuthenticated::class,
        'ceo' => \App\Http\Middleware\CeoAuthenticated::class,
        'ro' => \App\Http\Middleware\RoAuthenticated::class,
        'ro_only' => \App\Http\Middleware\RoMiddleware::class,
        'aro_only' => \App\Http\Middleware\AroMiddleware::class,
        'aro' => \App\Http\Middleware\ARoAuthenticated::class,
        //'cand' => \App\Http\Middleware\CandAuthenticated::class,
		'adminsession' => \App\Http\Middleware\CheckSession::class, //check session
        'usersession' => \App\Http\Middleware\CheckuserSession::class, //check session
		'clean_url' => \App\Http\Middleware\CleanUrl::class,
		
        'clean_request' => \App\Http\Middleware\CleanRequest::class,
		'eci_index' => \App\Http\Middleware\EciIndexMiddleware::class,
        'eci_expenditure' => \App\Http\Middleware\EciExpenditureMiddleware::class,
		'change_to_current' => \App\Http\Middleware\SetCurrentElectionDB_Middleware::class,
         'Encrypt' => \App\Http\Middleware\Encrypt::class,
        'XSS' => \App\Http\Middleware\XSS::class,
        'by_pass_security' => \App\Http\Middleware\ByPassSecurityAuditRequest::class,
    ];
}
