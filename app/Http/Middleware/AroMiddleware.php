<?php
namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Support\Facades\Auth;

class AroMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
	{
        if(\Auth::user() && \Auth::user()->role_id != '20' && \Auth::user()->role_id != '21'){
            return redirect('/logout');
        }
		
		$headers = [
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '86400',
                'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
            ];
    
            if ($request->isMethod('OPTIONS')) {
                return response()->json('{"method":"OPTIONS"}', 200, $headers);
            }
     
            $response = $next($request);
    
            foreach($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
     
            return $response;
        //return $next($request);
    }
}
