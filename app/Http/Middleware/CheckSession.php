<?php
namespace App\Http\Middleware;
use Closure;

class CheckSession
{

  public function handle($request, Closure $next)
    {  
	  if($request->session()->has('admin_login'))
		{ 
//			return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    $response = $next($request);
                  $headers = [
                      'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
                      'Pragma', 'no-cache',
                      'Expires', 'Fri, 01 Jan 1990 00:00:00 GMT',
                  ];

                  foreach ($headers as $key => $value) {
                      $response->headers->set($key, $value);
                  }
                  return $response;
		}

        return redirect('/officer-login');
    }

}