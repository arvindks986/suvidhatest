<?php
namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::user() && \Auth::user()->role_id != '18' && \Auth::user()->role_id != '22'){
            return redirect('/logout');
        }

        return $next($request);
    }
}
